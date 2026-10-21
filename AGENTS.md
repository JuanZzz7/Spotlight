## Contexto
Estoy trabajando sobre Spotlight, una plataforma web en Laravel (PHP 8.x, MySQL, Bootstrap, Blade) 
para visibilidad digital de negocios en Ubaté. Necesito dos actualizaciones.

### 1. Rediseño visual del frontend (landing page y vistas públicas)
- Problema actual: el front se ve demasiado corporativo/serio, con una estética que 
  parece más de tienda de insumos militares que de una plataforma cercana para 
  pequeños comerciantes y compradores locales.
- Objetivo: que se vea más llamativo, cálido y cercano al usuario, sin perder 
  profesionalismo.
- Restricción: NO cambiar la paleta de colores actual — mantenerla intacta.
- Qué sí puede cambiar: tipografía, iconografía, imágenes/ilustraciones, espaciados. 
  formas (bordes redondeados vs. rectos), microinteracciones, jerarquía visual.
- Alcance: revisar landing page y, si aplica, layouts Blade compartidos para que el 
  cambio de estilo se propague de forma consistente en todo el sitio.

### 2. Sistema de etiquetas/categorías de productos + filtro con animación
- Para comerciantes: permitir que cada negocio etiquete/categorice los productos que 
  vende (ej. comida, ropa, tecnología, hogar, etc.). Necesito un catálogo más amplio 
  de etiquetas, no solo unas pocas genéricas.
- Para compradores: al usar el filtro de búsqueda y seleccionar una categoría, debe 
  dispararse una animación de entrada a esa sección, temática según la categoría 
  elegida (ej. si filtra "comida", la animación usa tonos cálidos —naranjas/rojos— 
  y los productos de esa categoría entran con esa temática visual).
- Requiero: modelo de datos para etiquetas (relación producto-etiqueta, probablemente 
  muchos a muchos), UI para que el comerciante asigne etiquetas al crear/editar un 
  producto, UI de filtro para el comprador, y la transición animada al aplicar el filtro.
- Preferencia técnica: algo ligero y compatible con el stack actual (Bootstrap 5 + 
  Blade) — CSS/transiciones nativas o una librería ligera si ya existe una en el 
  proyecto. Evitar sumar un framework de animación pesado si no es estrictamente necesario.

### Entregable esperado
Antes de tocar código, preséntame un plan de implementación: archivos afectados, 
migraciones necesarias para las etiquetas, y el enfoque técnico de la animación. 
Espero tu aprobación antes de aplicar los cambios.