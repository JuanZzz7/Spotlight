<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BusinessExpenseCrudTest extends TestCase
{
    use RefreshDatabase;

    private function createMerchant(): array
    {
        $user = User::factory()->create([
            'role' => 'business',
            'email' => 'merchant_' . uniqid() . '@spotlight.local',
        ]);

        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Empresa Test ' . uniqid(),
            'tipo_negocio' => 'formal',
            'razon_social' => 'Empresa Test SAS',
            'nit' => '900998877-1',
            'status' => 'active',
            'address' => 'Cra 5 # 10-20, Ubaté',
            'phone' => '3109988776',
            'email' => 'empresa@test.local',
        ]);

        return [$user, $company];
    }

    public function test_merchant_can_create_expense_with_invoice_file(): void
    {
        Storage::fake('public');
        [$user, $company] = $this->createMerchant();

        $file = UploadedFile::fake()->create('factura_compra_01.pdf', 200, 'application/pdf');

        $response = $this->actingAs($user)->post(route('business.expenses.store'), [
            'concept'     => 'Insumos de empaque al vacío',
            'category'    => 'Insumos de Producción',
            'amount'      => 185000,
            'date'        => now()->format('Y-m-d'),
            'description' => 'Factura proveedor Envases del Valle',
            'invoice'     => $file,
        ]);

        $response->assertRedirect(route('business.expenses.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('expenses', [
            'company_id'       => $company->id,
            'concept'          => 'Insumos de empaque al vacío',
            'category'         => 'Insumos de Producción',
            'amount'           => 185000,
            'invoice_filename' => 'factura_compra_01.pdf',
        ]);

        $expense = Expense::where('company_id', $company->id)->first();
        $this->assertNotNull($expense->invoice_path);
        Storage::disk('public')->assertExists($expense->invoice_path);
    }

    public function test_merchant_can_view_expense_and_modal_in_index(): void
    {
        Storage::fake('public');
        [$user, $company] = $this->createMerchant();

        $file = UploadedFile::fake()->create('recibo_caja.png', 100, 'image/png');
        $path = $file->store('expenses/invoices', 'public');

        $expense = Expense::create([
            'company_id'       => $company->id,
            'concept'          => 'Publicidad en Redes Sociales',
            'category'         => 'Marketing Digital',
            'amount'           => 90000,
            'date'             => now()->format('Y-m-d'),
            'description'      => 'Campaña Meta Ads Ubaté',
            'invoice_path'     => $path,
            'invoice_filename' => 'recibo_caja.png',
        ]);

        $response = $this->actingAs($user)->get(route('business.expenses.index'));

        $response->assertStatus(200);
        $response->assertSee('Publicidad en Redes Sociales');
        $response->assertSee('Marketing Digital');
        $response->assertSee('$90.000');
        $response->assertSee('Ver Soporte');
        $response->assertSee('invoiceModal' . $expense->id);
    }

    public function test_merchant_can_edit_expense_and_replace_invoice(): void
    {
        Storage::fake('public');
        [$user, $company] = $this->createMerchant();

        $initialFile = UploadedFile::fake()->create('factura_inicial.pdf', 100, 'application/pdf');
        $initialPath = $initialFile->store('expenses/invoices', 'public');

        $expense = Expense::create([
            'company_id'       => $company->id,
            'concept'          => 'Gasto de Transporte',
            'category'         => 'Distribución & Logística',
            'amount'           => 45000,
            'date'             => now()->format('Y-m-d'),
            'invoice_path'     => $initialPath,
            'invoice_filename' => 'factura_inicial.pdf',
        ]);

        // Formulario de edición
        $editResponse = $this->actingAs($user)->get(route('business.expenses.edit', $expense->id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Editar Egreso #' . $expense->id);
        $editResponse->assertSee('factura_inicial.pdf');

        // Actualizar con nuevo archivo
        $newFile = UploadedFile::fake()->create('factura_corregida.pdf', 150, 'application/pdf');

        $updateResponse = $this->actingAs($user)->put(route('business.expenses.update', $expense->id), [
            'concept'     => 'Flete y Distribución Ubaté-Bogotá',
            'category'    => 'Distribución & Logística',
            'amount'      => 60000,
            'date'        => now()->format('Y-m-d'),
            'description' => 'Servicio intermunicipal de carga refrigerada',
            'invoice'     => $newFile,
        ]);

        $updateResponse->assertRedirect(route('business.expenses.index'));

        $expense->refresh();
        $this->assertEquals('Flete y Distribución Ubaté-Bogotá', $expense->concept);
        $this->assertEquals(60000, (float)$expense->amount);
        $this->assertEquals('factura_corregida.pdf', $expense->invoice_filename);

        Storage::disk('public')->assertExists($expense->invoice_path);
        Storage::disk('public')->assertMissing($initialPath);
    }

    public function test_merchant_can_export_expenses_to_pdf(): void
    {
        [$user, $company] = $this->createMerchant();

        Expense::create([
            'company_id' => $company->id,
            'concept'    => 'Mantenimiento de Cuarto Frío',
            'category'   => 'Mantenimiento & Equipos',
            'amount'     => 320000,
            'date'       => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($user)->get(route('business.expenses.export_pdf'));

        $response->assertStatus(200);
        $response->assertSee('Reporte de Egresos');
        $response->assertSee('Mantenimiento de Cuarto Frío');
        $response->assertSee('Mantenimiento & Equipos');
        $response->assertSee('$320.000');
        $response->assertSee('window.print()');
    }

    public function test_merchant_cannot_edit_or_delete_other_company_expense(): void
    {
        [$user1, $company1] = $this->createMerchant();
        [$user2, $company2] = $this->createMerchant();

        $expenseOfComp1 = Expense::create([
            'company_id' => $company1->id,
            'concept'    => 'Gasto Privado Comp 1',
            'amount'     => 50000,
            'date'       => now()->format('Y-m-d'),
        ]);

        // Usuario 2 no puede editar el gasto de Empresa 1
        $editResponse = $this->actingAs($user2)->get(route('business.expenses.edit', $expenseOfComp1->id));
        $editResponse->assertStatus(403);

        $updateResponse = $this->actingAs($user2)->put(route('business.expenses.update', $expenseOfComp1->id), [
            'concept' => 'Hack',
            'amount'  => 100,
            'date'    => now()->format('Y-m-d'),
        ]);
        $updateResponse->assertStatus(403);

        // Usuario 2 no puede eliminar el gasto de Empresa 1
        $deleteResponse = $this->actingAs($user2)->delete(route('business.expenses.destroy', $expenseOfComp1->id));
        $deleteResponse->assertStatus(403);
    }
}
