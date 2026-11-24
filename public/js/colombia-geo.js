/**
 * Spotlight - Selector de Ubicación Geográfica Precisa
 * Provee catálogo de Departamentos y Municipios para Colombia (y Estados para México)
 * con coordenadas centrales y geocodificación acotada para evitar ubicaciones erróneas.
 */

window.SPOTLIGHT_GEO_DATA = {
    CO: {
        defaultState: 'Cundinamarca',
        defaultCity: 'Ubaté',
        departments: {
            'Cundinamarca': {
                center: [5.3086, -73.8149],
                zoom: 12,
                cities: {
                    'Ubaté': [5.3086, -73.8149],
                    'Chía': [4.8625, -74.0573],
                    'Zipaquirá': [5.0264, -73.9996],
                    'Cajicá': [4.9189, -74.0278],
                    'Sopó': [4.9083, -73.9406],
                    'Cota': [4.8108, -74.1017],
                    'Tocancipá': [4.9667, -73.9167],
                    'Gachancipá': [4.9897, -73.8744],
                    'Cogua': [5.0617, -73.9789],
                    'Nemocón': [5.0689, -73.8797],
                    'Simijaca': [5.5033, -73.8522],
                    'Susa': [5.4522, -73.8139],
                    'Guachetá': [5.3789, -73.6842],
                    'Carmen de Carupa': [5.3486, -73.9056],
                    'Fúquene': [5.4194, -73.7486],
                    'Lenguazaque': [5.3047, -73.7153],
                    'Cucunubá': [5.2589, -73.7911],
                    'Tausa': [5.1953, -73.8797],
                    'Sutatausa': [5.2475, -73.8522],
                    'Facatativá': [4.8144, -74.3542],
                    'Fusagasugá': [4.3468, -74.3644],
                    'Soacha': [4.5800, -74.2167],
                    'Mosquera': [4.7058, -74.2303],
                    'Funza': [4.7175, -74.2117],
                    'Madrid': [4.7328, -74.2644],
                    'La Calera': [4.7208, -73.9708],
                    'Tabio': [4.9192, -74.0950],
                    'Tenjo': [4.8722, -74.1436],
                    'Subachoque': [4.9286, -74.1736],
                    'Girardot': [4.3014, -74.8028],
                    'Villeta': [5.0117, -74.4744],
                    'Pacho': [5.1328, -74.1611],
                    'Guaduas': [5.0694, -74.5967]
                }
            },
            'Bogotá D.C.': {
                center: [4.6097, -74.0817],
                zoom: 12,
                cities: {
                    'Bogotá D.C.': [4.6097, -74.0817],
                    'Usaquén': [4.6989, -74.0306],
                    'Chapinero': [4.6469, -74.0628],
                    'Suba': [4.7439, -74.0847],
                    'Teusaquillo': [4.6400, -74.0850],
                    'Barrios Unidos': [4.6667, -74.0750],
                    'Fontibón': [4.6750, -74.1444],
                    'Engativá': [4.7083, -74.1167],
                    'Kennedy': [4.6300, -74.1500],
                    'Bosa': [4.6186, -74.1906],
                    'Santa Fe': [4.6000, -74.0700],
                    'La Candelaria': [4.5969, -74.0736],
                    'Puente Aranda': [4.6214, -74.1133],
                    'Ciudad Bolívar': [4.5500, -74.1600]
                }
            },
            'Boyacá': {
                center: [5.5353, -73.3678],
                zoom: 12,
                cities: {
                    'Tunja': [5.5353, -73.3678],
                    'Duitama': [5.8267, -73.0333],
                    'Sogamoso': [5.7144, -72.9339],
                    'Chiquinquirá': [5.6175, -73.8164],
                    'Paipa': [5.7800, -73.1167],
                    'Villa de Leyva': [5.6331, -73.5267],
                    'Moniquirá': [5.8756, -73.5714],
                    'Nobsa': [5.7708, -72.9392],
                    'Tibasosa': [5.7486, -72.9994],
                    'Samacá': [5.4950, -73.4867],
                    'Ráquira': [5.5375, -73.6319],
                    'Saboyá': [5.7000, -73.7667],
                    'San Miguel de Sema': [5.5186, -73.7228]
                }
            },
            'Antioquia': {
                center: [6.2442, -75.5812],
                zoom: 12,
                cities: {
                    'Medellín': [6.2442, -75.5812],
                    'Envigado': [6.1667, -75.5833],
                    'Itagüí': [6.1833, -75.6000],
                    'Bello': [6.3333, -75.5667],
                    'Sabaneta': [6.1500, -75.6167],
                    'Rionegro': [6.1539, -75.3742],
                    'La Ceja': [6.0319, -75.4267],
                    'Marinilla': [6.1750, -75.3333],
                    'Guarne': [6.2800, -75.4433]
                }
            },
            'Santander': {
                center: [7.1254, -73.1198],
                zoom: 12,
                cities: {
                    'Bucaramanga': [7.1254, -73.1198],
                    'Floridablanca': [7.0622, -73.0864],
                    'Girón': [7.0706, -73.1697],
                    'Piedecuesta': [6.9886, -73.0503],
                    'San Gil': [6.5567, -73.1367],
                    'Barrancabermeja': [7.0653, -73.8547],
                    'Socorro': [6.4636, -73.2611]
                }
            },
            'Valle del Cauca': {
                center: [3.4516, -76.5320],
                zoom: 12,
                cities: {
                    'Cali': [3.4516, -76.5320],
                    'Palmira': [3.5394, -76.3036],
                    'Buenaventura': [3.8833, -77.0333],
                    'Tuluá': [4.0847, -76.1956],
                    'Buga': [3.9008, -76.2978],
                    'Jamundí': [3.2606, -76.5414],
                    'Yumbo': [3.5828, -76.4914],
                    'Cartago': [4.7464, -75.9117]
                }
            },
            'Atlántico': {
                center: [10.9685, -74.7813],
                zoom: 12,
                cities: {
                    'Barranquilla': [10.9685, -74.7813],
                    'Soledad': [10.9167, -74.7667],
                    'Malambo': [10.8589, -74.7739],
                    'Puerto Colombia': [10.9878, -74.9547],
                    'Baranoa': [10.7936, -74.9164]
                }
            },
            'Bolívar': {
                center: [10.3910, -75.4794],
                zoom: 12,
                cities: {
                    'Cartagena': [10.3910, -75.4794],
                    'Turbaco': [10.3275, -75.4128],
                    'Arjona': [10.2567, -75.3444],
                    'Magangué': [9.2417, -74.7547]
                }
            },
            'Tolima': {
                center: [4.4389, -75.2322],
                zoom: 12,
                cities: {
                    'Ibagué': [4.4389, -75.2322],
                    'Espinal': [4.1492, -74.8842],
                    'Melgar': [4.2047, -74.6408],
                    'Chaparral': [3.7225, -75.5833],
                    'Mariquita': [5.2000, -74.9000]
                }
            },
            'Meta': {
                center: [4.1420, -73.6266],
                zoom: 12,
                cities: {
                    'Villavicencio': [4.1420, -73.6266],
                    'Acacías': [3.9872, -73.7578],
                    'Granada': [3.5458, -73.7067]
                }
            },
            'Risaralda': {
                center: [4.8133, -75.6961],
                zoom: 12,
                cities: {
                    'Pereira': [4.8133, -75.6961],
                    'Dosquebradas': [4.8333, -75.6833],
                    'Santa Rosa de Cabal': [4.8683, -75.6214]
                }
            },
            'Caldas': {
                center: [5.0689, -75.5174],
                zoom: 12,
                cities: {
                    'Manizales': [5.0689, -75.5174],
                    'Villamaría': [5.0456, -75.5156],
                    'Chinchiná': [4.9819, -75.6033]
                }
            },
            'Quindío': {
                center: [4.5339, -75.6811],
                zoom: 12,
                cities: {
                    'Armenia': [4.5339, -75.6811],
                    'Calarcá': [4.5297, -75.6417],
                    'Montenegro': [4.5667, -75.7500],
                    'Quimbaya': [4.6231, -75.7633],
                    'Circasia': [4.6186, -75.6358],
                    'Salento': [4.6375, -75.5708]
                }
            },
            'Huila': {
                center: [2.9273, -75.2819],
                zoom: 12,
                cities: {
                    'Neiva': [2.9273, -75.2819],
                    'Pitalito': [1.8533, -76.0494],
                    'Garzón': [2.1958, -75.6278]
                }
            },
            'Norte de Santander': {
                center: [7.8939, -72.5078],
                zoom: 12,
                cities: {
                    'Cúcuta': [7.8939, -72.5078],
                    'Los Patios': [7.8344, -72.5072],
                    'Villa del Rosario': [7.8339, -72.4744],
                    'Ocaña': [8.2389, -73.3547],
                    'Pamplona': [7.3756, -72.6481]
                }
            },
            'Nariño': {
                center: [1.2136, -77.2811],
                zoom: 12,
                cities: {
                    'Pasto': [1.2136, -77.2811],
                    'Ipiales': [0.8286, -77.6436],
                    'Tumaco': [1.7986, -78.8156]
                }
            },
            'Cesar': {
                center: [10.4631, -73.2532],
                zoom: 12,
                cities: {
                    'Valledupar': [10.4631, -73.2532],
                    'Aguachica': [8.3078, -73.6167]
                }
            },
            'Córdoba': {
                center: [8.7479, -75.8814],
                zoom: 12,
                cities: {
                    'Montería': [8.7479, -75.8814],
                    'Cereté': [8.8842, -75.7925],
                    'Lorica': [9.2392, -75.8139]
                }
            },
            'Magdalena': {
                center: [11.2408, -74.1990],
                zoom: 12,
                cities: {
                    'Santa Marta': [11.2408, -74.1990],
                    'Ciénaga': [11.0067, -74.2494]
                }
            },
            'Cauca': {
                center: [2.4419, -76.6064],
                zoom: 12,
                cities: {
                    'Popayán': [2.4419, -76.6064],
                    'Santander de Quilichao': [3.0094, -76.4847]
                }
            },
            'Casanare': {
                center: [5.3378, -72.3958],
                zoom: 12,
                cities: {
                    'Yopal': [5.3378, -72.3958],
                    'Aguazul': [5.1728, -72.5469],
                    'Villanueva': [4.6064, -72.9281]
                }
            },
            'La Guajira': {
                center: [11.5444, -72.9072],
                zoom: 12,
                cities: {
                    'Riohacha': [11.5444, -72.9072],
                    'Maicao': [11.3783, -72.2417]
                }
            },
            'Sucre': {
                center: [9.3047, -75.3978],
                zoom: 12,
                cities: {
                    'Sincelejo': [9.3047, -75.3978],
                    'Corozal': [9.3167, -75.2833]
                }
            },
            'Caquetá': {
                center: [1.6144, -75.6064],
                zoom: 12,
                cities: { 'Florencia': [1.6144, -75.6064] }
            },
            'Putumayo': {
                center: [1.1486, -76.6494],
                zoom: 12,
                cities: { 'Mocoa': [1.1486, -76.6494], 'Puerto Asís': [0.5133, -76.5008] }
            },
            'Arauca': {
                center: [7.0847, -70.7592],
                zoom: 12,
                cities: { 'Arauca': [7.0847, -70.7592], 'Tame': [6.4608, -71.7300] }
            },
            'Chocó': {
                center: [5.6919, -76.6583],
                zoom: 12,
                cities: { 'Quibdó': [5.6919, -76.6583] }
            },
            'San Andrés y Providencia': {
                center: [12.5847, -81.7006],
                zoom: 13,
                cities: { 'San Andrés': [12.5847, -81.7006] }
            },
            'Amazonas': {
                center: [-4.2153, -69.9406],
                zoom: 12,
                cities: { 'Leticia': [-4.2153, -69.9406] }
            },
            'Guaviare': {
                center: [2.5728, -72.6458],
                zoom: 12,
                cities: { 'San José del Guaviare': [2.5728, -72.6458] }
            }
        }
    },
    MX: {
        defaultState: 'Puebla',
        defaultCity: 'Puebla',
        departments: {
            'Puebla': {
                center: [19.0414, -98.2063],
                zoom: 12,
                cities: {
                    'Puebla': [19.0414, -98.2063],
                    'Juan C. Bonilla': [19.1069, -98.3347],
                    'San Andrés Cholula': [19.0558, -98.3000],
                    'San Pedro Cholula': [19.0628, -98.3075],
                    'Cuautlancingo': [19.0911, -98.2778],
                    'Coronango': [19.1231, -98.3069],
                    'Huejotzingo': [19.1586, -98.4069],
                    'Atlixco': [18.9083, -98.4333],
                    'San Martín Texmelucan': [19.2842, -98.4347],
                    'Amozoc': [19.0400, -98.0500],
                    'San Gregorio Atzompa': [19.0233, -98.3475],
                    'Ocoyucan': [18.9750, -98.3000],
                    'Calpan': [19.1044, -98.4619],
                    'Tlaltenango': [19.1714, -98.3419],
                    'San Miguel Xoxtla': [19.1711, -98.3056],
                    'Tehuacán': [18.4633, -97.3933],
                    'Teziutlán': [19.8167, -97.3667],
                    'Huauchinango': [20.1772, -98.0528],
                    'Zacatlán': [19.9328, -97.9608],
                    'Chignahuapan': [19.8378, -98.0322],
                    'Izúcar de Matamoros': [18.6014, -98.4633],
                    'Tepeaca': [18.9667, -97.9000],
                    'Tecamachalco': [18.8833, -97.7333],
                    'Acatzingo': [18.9811, -97.7817],
                    'Acajete': [19.1050, -97.9511],
                    'Ciudad Serdán': [18.9889, -97.4467],
                    'Cuetzalan del Progreso': [20.0181, -97.5228],
                    'Zacapoaxtla': [19.8736, -97.5875],
                    'Tlatlauquitepec': [19.8511, -97.4956],
                    'Xicotepec': [20.2764, -97.9575]
                }
            },
            'Ciudad de México': {
                center: [19.4326, -99.1332],
                zoom: 12,
                cities: {
                    'Cuauhtémoc': [19.4326, -99.1332],
                    'Benito Juárez': [19.3800, -99.1600],
                    'Miguel Hidalgo': [19.4100, -99.2000],
                    'Coyoacán': [19.3500, -99.1600],
                    'Álvaro Obregón': [19.3600, -99.2000],
                    'Tlalpan': [19.2889, -99.1667],
                    'Iztapalapa': [19.3556, -99.0917],
                    'Gustavo A. Madero': [19.4833, -99.1167],
                    'Venustiano Carranza': [19.4333, -99.0833],
                    'Azcapotzalco': [19.4833, -99.1833],
                    'Cuajimalpa': [19.3667, -99.3000],
                    'Xochimilco': [19.2556, -99.1028]
                }
            },
            'Estado de México': {
                center: [19.2925, -99.6569],
                zoom: 12,
                cities: {
                    'Toluca': [19.2925, -99.6569],
                    'Naucalpan de Juárez': [19.4786, -99.2392],
                    'Tlalnepantla': [19.5400, -99.1950],
                    'Ecatepec': [19.6097, -99.0600],
                    'Nezahualcóyotl': [19.4000, -99.0167],
                    'Metepec': [19.2564, -99.6047],
                    'Huixquilucan': [19.3575, -99.3517],
                    'Cuautitlán Izcalli': [19.6458, -99.2150],
                    'Chimalhuacán': [19.4367, -98.9489],
                    'Atizapán de Zaragoza': [19.5583, -99.2514]
                }
            },
            'Jalisco': {
                center: [20.6597, -103.3496],
                zoom: 12,
                cities: {
                    'Guadalajara': [20.6597, -103.3496],
                    'Zapopan': [20.7167, -103.4000],
                    'Tlaquepaque': [20.6394, -103.3131],
                    'Tonalá': [20.6247, -103.2356],
                    'Tlajomulco de Zúñiga': [20.4739, -103.4447],
                    'Puerto Vallarta': [20.6534, -105.2253],
                    'Lagos de Moreno': [21.3556, -101.9306],
                    'Tepatitlán de Morelos': [20.8167, -102.7667]
                }
            },
            'Nuevo León': {
                center: [25.6866, -100.3161],
                zoom: 12,
                cities: {
                    'Monterrey': [25.6866, -100.3161],
                    'San Pedro Garza García': [25.6572, -100.4022],
                    'San Nicolás de los Garza': [25.7500, -100.3000],
                    'Guadalupe': [25.6778, -100.2597],
                    'Apodaca': [25.7817, -100.1883],
                    'Santa Catarina': [25.6764, -100.4639],
                    'General Escobedo': [25.7958, -100.3208]
                }
            },
            'Querétaro': {
                center: [20.5888, -100.3899],
                zoom: 12,
                cities: {
                    'Santiago de Querétaro': [20.5888, -100.3899],
                    'San Juan del Río': [20.3889, -99.9958],
                    'Corregidora': [20.5333, -100.4333],
                    'El Marqués': [20.6728, -100.2858],
                    'Tequisquiapan': [20.5217, -99.8928]
                }
            },
            'Guanajuato': {
                center: [21.0190, -101.2574],
                zoom: 12,
                cities: {
                    'León': [21.1222, -101.6822],
                    'Irapuato': [20.6736, -101.3564],
                    'Celaya': [20.5278, -100.8158],
                    'Salamanca': [20.5728, -101.1969],
                    'Guanajuato': [21.0190, -101.2574],
                    'San Miguel de Allende': [20.9144, -100.7453],
                    'Silao': [20.9436, -101.4278]
                }
            },
            'Veracruz': {
                center: [19.1738, -96.1342],
                zoom: 12,
                cities: {
                    'Veracruz': [19.1738, -96.1342],
                    'Xalapa-Enríquez': [19.5438, -96.9102],
                    'Boca del Río': [19.1083, -96.1083],
                    'Coatzacoalcos': [18.1500, -94.4333],
                    'Córdoba': [18.8944, -96.9353],
                    'Orizaba': [18.8500, -97.1000],
                    'Poza Rica': [20.5333, -97.4500]
                }
            },
            'Oaxaca': {
                center: [17.0732, -96.7266],
                zoom: 12,
                cities: {
                    'Oaxaca de Juárez': [17.0732, -96.7266],
                    'Santa Cruz Huatulco': [15.7667, -96.1333],
                    'Puerto Escondido': [15.8625, -97.0667],
                    'Salina Cruz': [16.1833, -95.2000],
                    'Juchitán de Zaragoza': [16.4333, -95.0167]
                }
            },
            'Yucatán': {
                center: [20.9674, -89.5926],
                zoom: 12,
                cities: {
                    'Mérida': [20.9674, -89.5926],
                    'Valladolid': [20.6897, -88.2014],
                    'Progreso': [21.2833, -89.6667],
                    'Tizimín': [21.1428, -88.1508]
                }
            },
            'Quintana Roo': {
                center: [21.1619, -86.8515],
                zoom: 12,
                cities: {
                    'Cancún': [21.1619, -86.8515],
                    'Playa del Carmen': [20.6296, -87.0739],
                    'Tulum': [20.2114, -87.4654],
                    'Cozumel': [20.5083, -86.9528],
                    'Chetumal': [18.5036, -88.3053]
                }
            },
            'Baja California': {
                center: [32.5149, -117.0382],
                zoom: 12,
                cities: {
                    'Tijuana': [32.5149, -117.0382],
                    'Mexicali': [32.6519, -115.4683],
                    'Ensenada': [31.8667, -116.6000],
                    'Playas de Rosarito': [32.3667, -117.0500],
                    'Tecate': [32.5667, -116.6333]
                }
            },
            'Baja California Sur': {
                center: [24.1426, -110.3128],
                zoom: 12,
                cities: {
                    'La Paz': [24.1426, -110.3128],
                    'Los Cabos': [22.8905, -109.9167],
                    'San José del Cabo': [23.0617, -109.7083]
                }
            },
            'Chihuahua': {
                center: [28.6353, -106.0889],
                zoom: 12,
                cities: {
                    'Chihuahua': [28.6353, -106.0889],
                    'Ciudad Juárez': [31.6904, -106.4245],
                    'Delicias': [28.1914, -105.4711],
                    'Cuauhtémoc': [28.4069, -106.8664]
                }
            },
            'Coahuila': {
                center: [25.4232, -101.0053],
                zoom: 12,
                cities: {
                    'Saltillo': [25.4232, -101.0053],
                    'Torreón': [25.5428, -103.4068],
                    'Monclova': [26.9069, -101.4208],
                    'Piedras Negras': [28.7000, -100.5231]
                }
            },
            'Sonora': {
                center: [29.0729, -110.9559],
                zoom: 12,
                cities: {
                    'Hermosillo': [29.0729, -110.9559],
                    'Ciudad Obregón': [27.4864, -109.9408],
                    'Nogales': [31.3086, -110.9422],
                    'San Luis Río Colorado': [32.4561, -114.7719]
                }
            },
            'Sinaloa': {
                center: [24.8091, -107.3940],
                zoom: 12,
                cities: {
                    'Culiacán': [24.8091, -107.3940],
                    'Mazatlán': [23.2494, -106.4111],
                    'Los Mochis': [25.7928, -108.9958]
                }
            },
            'Michoacán': {
                center: [19.7060, -101.1950],
                zoom: 12,
                cities: {
                    'Morelia': [19.7060, -101.1950],
                    'Uruapan': [19.4167, -102.0667],
                    'Zamora': [19.9833, -102.2833]
                }
            },
            'Hidalgo': {
                center: [20.1225, -98.7364],
                zoom: 12,
                cities: {
                    'Pachuca de Soto': [20.1225, -98.7364],
                    'Tulancingo': [20.0833, -98.3667],
                    'Tula de Allende': [20.0533, -99.3439]
                }
            },
            'Morelos': {
                center: [18.9261, -99.2308],
                zoom: 12,
                cities: {
                    'Cuernavaca': [18.9261, -99.2308],
                    'Jiutepec': [18.8833, -99.1833],
                    'Cuautla': [18.8128, -98.9556]
                }
            },
            'Tlaxcala': {
                center: [19.3181, -98.2375],
                zoom: 12,
                cities: {
                    'Tlaxcala': [19.3181, -98.2375],
                    'Apizaco': [19.4167, -98.1333],
                    'Huamantla': [19.3167, -97.9167],
                    'Chiautempan': [19.3167, -98.1833]
                }
            },
            'Aguascalientes': {
                center: [21.8853, -102.2916],
                zoom: 12,
                cities: {
                    'Aguascalientes': [21.8853, -102.2916],
                    'Jesús María': [21.9611, -102.3436]
                }
            },
            'San Luis Potosí': {
                center: [22.1565, -100.9855],
                zoom: 12,
                cities: {
                    'San Luis Potosí': [22.1565, -100.9855],
                    'Soledad de Graciano Sánchez': [22.1833, -100.9333],
                    'Ciudad Valles': [21.9833, -99.0167]
                }
            },
            'Tamaulipas': {
                center: [26.0444, -98.2914],
                zoom: 12,
                cities: {
                    'Reynosa': [26.0444, -98.2914],
                    'Matamoros': [25.8697, -97.5028],
                    'Nuevo Laredo': [27.4764, -99.5164],
                    'Tampico': [22.2556, -97.8686],
                    'Ciudad Victoria': [23.7369, -99.1411]
                }
            },
            'Tabasco': {
                center: [17.9895, -92.9475],
                zoom: 12,
                cities: {
                    'Villahermosa': [17.9895, -92.9475],
                    'Cárdenas': [18.0000, -93.3833]
                }
            },
            'Chiapas': {
                center: [16.7569, -93.1292],
                zoom: 12,
                cities: {
                    'Tuxtla Gutiérrez': [16.7569, -93.1292],
                    'Tapachula': [14.9042, -92.2611],
                    'San Cristóbal de las Casas': [16.7370, -92.6376]
                }
            },
            'Guerrero': {
                center: [16.8531, -99.8237],
                zoom: 12,
                cities: {
                    'Acapulco': [16.8531, -99.8237],
                    'Chilpancingo': [17.5500, -99.5000],
                    'Zihuatanejo': [17.6436, -101.5517]
                }
            },
            'Zacatecas': {
                center: [22.7709, -102.5832],
                zoom: 12,
                cities: {
                    'Zacatecas': [22.7709, -102.5832],
                    'Fresnillo': [23.1747, -102.8689],
                    'Guadalupe': [22.7486, -102.5097]
                }
            },
            'Durango': {
                center: [24.0277, -104.6532],
                zoom: 12,
                cities: {
                    'Durango': [24.0277, -104.6532],
                    'Gómez Palacio': [25.5647, -103.4989]
                }
            },
            'Colima': {
                center: [19.2452, -103.7241],
                zoom: 12,
                cities: {
                    'Colima': [19.2452, -103.7241],
                    'Manzanillo': [19.0522, -104.3158]
                }
            },
            'Nayarit': {
                center: [21.5089, -104.8953],
                zoom: 12,
                cities: {
                    'Tepic': [21.5089, -104.8953],
                    'Bahía de Banderas': [20.8000, -105.2500]
                }
            },
            'Campeche': {
                center: [19.8301, -90.5349],
                zoom: 12,
                cities: {
                    'Campeche': [19.8301, -90.5349],
                    'Ciudad del Carmen': [18.6439, -91.8292]
                }
            }
        }
    }
};

/**
 * Inicializador del componente de ubicación precisa
 */
window.initSpotlightPreciseLocation = function(cfg) {
    const country = cfg.country || 'CO';
    const geo = window.SPOTLIGHT_GEO_DATA[country] || window.SPOTLIGHT_GEO_DATA.CO;

    const stateSelect = document.getElementById(cfg.stateSelectId);
    const citySelect = document.getElementById(cfg.citySelectId);
    const addressInput = document.getElementById(cfg.addressInputId);
    const postalCodeInput = cfg.postalCodeInputId ? document.getElementById(cfg.postalCodeInputId) : null;
    const latInput = document.getElementById(cfg.latInputId);
    const lngInput = document.getElementById(cfg.lngInputId);
    const searchBtn = document.getElementById(cfg.searchBtnId);
    const gpsBtn = document.getElementById(cfg.gpsBtnId);
    const statusDisplay = document.getElementById(cfg.statusDisplayId);
    const mapElement = document.getElementById(cfg.mapId);

    if (!mapElement) return;

    let currentState = cfg.initialState || geo.defaultState;
    let currentCity = cfg.initialCity || geo.defaultCity;

    let initLat = parseFloat(cfg.initialLat);
    let initLng = parseFloat(cfg.initialLng);

    if (isNaN(initLat) || isNaN(initLng) || (initLat === 0 && initLng === 0)) {
        const defaultCityCoords = (geo.departments[currentState]?.cities[currentCity]) ||
                                  (geo.departments[currentState]?.center) ||
                                  [5.3086, -73.8149];
        initLat = defaultCityCoords[0];
        initLng = defaultCityCoords[1];
    }

    // 1. Población del Selector de Departamentos
    if (stateSelect) {
        stateSelect.innerHTML = '';
        Object.keys(geo.departments).forEach(deptName => {
            const opt = document.createElement('option');
            opt.value = deptName;
            opt.textContent = deptName;
            if (deptName.toLowerCase() === currentState.toLowerCase()) {
                opt.selected = true;
            }
            stateSelect.appendChild(opt);
        });
        currentState = stateSelect.value;
    }

    // 2. Población del Selector de Ciudades / Municipios
    function populateCities(deptName, keepSelectedCity = null) {
        if (!citySelect) return;
        citySelect.innerHTML = '';
        const deptObj = geo.departments[deptName];
        if (!deptObj) return;

        let matched = false;
        Object.keys(deptObj.cities).forEach(cityName => {
            const opt = document.createElement('option');
            opt.value = cityName;
            opt.textContent = cityName;
            if (keepSelectedCity && cityName.toLowerCase() === keepSelectedCity.toLowerCase()) {
                opt.selected = true;
                matched = true;
            }
            citySelect.appendChild(opt);
        });

        // Opción adicional para otro municipio no listado
        const optOther = document.createElement('option');
        optOther.value = '_other_';
        optOther.textContent = '+ Otro municipio...';
        citySelect.appendChild(optOther);

        if (!matched && keepSelectedCity && keepSelectedCity !== '_other_') {
            const customOpt = document.createElement('option');
            customOpt.value = keepSelectedCity;
            customOpt.textContent = keepSelectedCity;
            customOpt.selected = true;
            citySelect.insertBefore(customOpt, optOther);
        } else if (!matched) {
            citySelect.selectedIndex = 0;
        }

        currentCity = citySelect.value;
    }

    populateCities(currentState, currentCity);

    // 3. Inicialización del Mapa Leaflet
    delete L.Icon.Default.prototype._getIconUrl;
    L.Icon.Default.mergeOptions({
        iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
        iconUrl:       'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
        shadowUrl:     'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
    });

    const map = L.map(mapElement, {
        scrollWheelZoom: false
    }).setView([initLat, initLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    const marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);

    function updateFormCoords(lat, lng) {
        if (latInput) latInput.value = lat.toFixed(8);
        if (lngInput) lngInput.value = lng.toFixed(8);
    }
    updateFormCoords(initLat, initLng);

    // 4. Geocodificación Inversa (Reverse Geocoding)
    function reverseGeocode(lat, lng, preserveUserAddress = false) {
        if (statusDisplay) {
            statusDisplay.innerHTML = '<span class="text-muted"><i class="bi bi-arrow-repeat spin-me me-1"></i> Detectando dirección exacta en el mapa...</span>';
        }
        const lang = country === 'MX' ? 'es-MX' : 'es-CO';
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=${lang}`)
            .then(r => r.json())
            .then(data => {
                if (data && data.address) {
                    const a = data.address;
                    let cleanStreet = '';
                    if (a.road) cleanStreet += a.road;
                    if (a.house_number) cleanStreet += ' #' + a.house_number;
                    if (a.neighbourhood || a.suburb || a.residential) {
                        cleanStreet += (cleanStreet ? ', ' : '') + (a.neighbourhood || a.suburb || a.residential);
                    }

                    const detectedCity = a.town || a.city || a.municipality || a.village || a.county || currentCity;
                    const detectedState = a.state || currentState;

                    if (postalCodeInput && a.postcode && !postalCodeInput.value.trim()) {
                        postalCodeInput.value = a.postcode;
                    }

                    let finalLabel = cleanStreet
                        ? `${cleanStreet}, ${detectedCity}`
                        : (data.display_name ? data.display_name.split(',').slice(0, 3).join(', ') : `${lat.toFixed(5)}, ${lng.toFixed(5)}`);

                    if (statusDisplay) {
                        statusDisplay.innerHTML = `<span class="text-success"><i class="bi bi-geo-alt-fill text-danger me-1"></i><strong>Ubicación fijada:</strong> ${finalLabel} (${detectedState})</span>`;
                    }
                    if (addressInput && (!preserveUserAddress || !addressInput.value.trim())) {
                        addressInput.value = cleanStreet || finalLabel;
                    }
                } else {
                    const fallbackLabel = `${currentCity}, ${currentState} (${lat.toFixed(5)}, ${lng.toFixed(5)})`;
                    if (statusDisplay) {
                        statusDisplay.innerHTML = `<span class="text-dark"><i class="bi bi-geo-alt-fill text-danger me-1"></i><strong>Coordenadas fijadas:</strong> ${fallbackLabel}</span>`;
                    }
                    if (addressInput && !addressInput.value.trim()) {
                        addressInput.value = `${currentCity}, ${currentState}`;
                    }
                }
            })
            .catch(() => {
                const fallbackLabel = `${currentCity}, ${currentState} (${lat.toFixed(5)}, ${lng.toFixed(5)})`;
                if (statusDisplay) {
                    statusDisplay.innerHTML = `<span class="text-dark"><i class="bi bi-geo-alt-fill text-danger me-1"></i><strong>Coordenadas fijadas:</strong> ${fallbackLabel}</span>`;
                }
            });
    }

    // Auxiliares para normalizar municipios y direcciones (especialmente México y Colombia)
    function normalizeMunicipality(mun, dept) {
        if (!mun) return '';
        let cleaned = mun.trim();
        if (dept) {
            const escapedDept = dept.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const trailingDeptRegex = new RegExp(`[\\s,]+${escapedDept}$`, 'i');
            cleaned = cleaned.replace(trailingDeptRegex, '').trim();
        }
        return cleaned || mun.trim();
    }

    function findKnownCityCoords(dept, mun) {
        const deptObj = geo.departments[dept];
        if (!deptObj || !deptObj.cities) return null;
        if (deptObj.cities[mun]) return deptObj.cities[mun];

        const cleanMun = normalizeMunicipality(mun, dept).toLowerCase();
        for (const [cityName, coords] of Object.entries(deptObj.cities)) {
            if (cityName.toLowerCase() === cleanMun) {
                return coords;
            }
        }
        return null;
    }

    function stripInteriorDetails(rawAddress) {
        if (!rawAddress) return '';
        return rawAddress
            .replace(/\b(?:int(?:erior)?|depto|departamento|apt(?:o)?|local|loc|piso|nivel|edif(?:icio)?|torre|mz|manzana|lt|lote|casa|bodega|oficina|of)\.?\s*[a-z0-9\-]+/gi, ' ')
            .replace(/\b(?:no\.?|núm\.?|num\.?)\s*(\d+)/gi, '$1')
            .replace(/#(\d+)/g, ' $1')
            .replace(/\s{2,}/g, ' ')
            .replace(/[,\s]+$/g, '')
            .trim();
    }

    function stripHouseNumber(streetAddress) {
        if (!streetAddress) return '';
        return streetAddress
            .replace(/\s+\d+[a-z\-]*$/i, '')
            .trim();
    }

    async function queryNominatim(qStr, countryCode, extraParams = '') {
        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(qStr)}&countrycodes=${countryCode}&accept-language=es&limit=5${extraParams}`;
        const res = await fetch(url);
        if (!res.ok) return [];
        const data = await res.json();
        return Array.isArray(data) ? data : [];
    }

    // 5. Búsqueda Directa Inteligente en Cascada (Dirección + C.P. + Municipio + Estado)
    async function searchAddress() {
        const rawQuery = (addressInput?.value || '').trim();
        const postalCode = (postalCodeInput?.value || '').trim();
        const selectedDept = stateSelect ? stateSelect.value : currentState;
        const rawMun = citySelect ? citySelect.value : currentCity;
        const cleanMun = normalizeMunicipality(rawMun, selectedDept);

        if (statusDisplay) {
            statusDisplay.innerHTML = `<span class="text-primary"><i class="bi bi-search spin-me me-1"></i> Buscando ubicación en <strong>${cleanMun}, ${selectedDept}</strong>...</span>`;
        }

        const countryName = country === 'MX' ? 'México' : 'Colombia';
        const countryCode = country.toLowerCase();
        const cleanStreet = stripInteriorDetails(rawQuery);
        const streetOnly = stripHouseNumber(cleanStreet);

        // Construir cola de intentos desde el más específico al más general
        const attempts = [];

        if (cleanStreet.length > 0) {
            if (postalCode.length >= 4) {
                attempts.push({ q: `${cleanStreet}, ${postalCode}, ${cleanMun}, ${selectedDept}, ${countryName}`, exact: true, zoom: 17 });
            }
            attempts.push({ q: `${cleanStreet}, ${cleanMun}, ${selectedDept}, ${countryName}`, exact: true, zoom: 17 });

            if (rawQuery !== cleanStreet) {
                attempts.push({ q: `${rawQuery}, ${cleanMun}, ${selectedDept}, ${countryName}`, exact: true, zoom: 17 });
            }
            if (postalCode.length >= 4) {
                attempts.push({ q: `${cleanStreet}, ${postalCode}, ${selectedDept}, ${countryName}`, exact: true, zoom: 17 });
            }
            if (streetOnly.length >= 3 && streetOnly !== cleanStreet) {
                attempts.push({ q: `${streetOnly}, ${cleanMun}, ${selectedDept}, ${countryName}`, exact: true, zoom: 16 });
                if (postalCode.length >= 4) {
                    attempts.push({ q: `${streetOnly}, ${postalCode}, ${selectedDept}, ${countryName}`, exact: true, zoom: 16 });
                }
            }
        }

        if (postalCode.length >= 4) {
            attempts.push({ q: `${postalCode}, ${cleanMun}, ${selectedDept}, ${countryName}`, exact: false, label: `Código Postal ${postalCode} (${cleanMun})`, zoom: 15 });
            attempts.push({ q: `${postalCode}, ${selectedDept}, ${countryName}`, exact: false, label: `Código Postal ${postalCode}`, zoom: 15 });
        }

        attempts.push({ q: `${cleanMun}, ${selectedDept}, ${countryName}`, exact: false, label: cleanMun, zoom: 15 });

        try {
            for (const attempt of attempts) {
                const results = await queryNominatim(attempt.q, countryCode);
                if (results && results.length > 0) {
                    const top = results[0];
                    const lat = parseFloat(top.lat);
                    const lon = parseFloat(top.lon);

                    marker.setLatLng([lat, lon]);
                    map.flyTo([lat, lon], attempt.zoom || 16, { duration: 1.1 });
                    updateFormCoords(lat, lon);

                    const shortDisplay = top.display_name ? top.display_name.split(',').slice(0, 3).join(', ') : `${cleanMun}, ${selectedDept}`;

                    if (statusDisplay) {
                        if (attempt.exact) {
                            statusDisplay.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i><strong>Ubicación encontrada:</strong> ${shortDisplay}. Puedes arrastrar el pin azul para mayor precisión.</span>`;
                        } else if (rawQuery.length > 0) {
                            statusDisplay.innerHTML = `<span class="text-dark"><i class="bi bi-geo-alt-fill me-1" style="color:#7F1D3F;"></i><strong>Ubicado en ${attempt.label || cleanMun}:</strong> (${shortDisplay}). Arrastra el pin azul al punto exacto de tu calle.</span>`;
                        } else {
                            statusDisplay.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i><strong>Zona ubicada:</strong> ${shortDisplay}</span>`;
                        }
                    }
                    return;
                }
            }

            // Fallback a coordenadas conocidas del catálogo si Nominatim no respondió
            const fallbackCoords = findKnownCityCoords(selectedDept, rawMun) || geo.departments[selectedDept]?.center;
            if (fallbackCoords) {
                marker.setLatLng(fallbackCoords);
                map.flyTo(fallbackCoords, 15, { duration: 1.0 });
                updateFormCoords(fallbackCoords[0], fallbackCoords[1]);
                if (statusDisplay) {
                    statusDisplay.innerHTML = `<span class="text-dark"><i class="bi bi-geo-alt-fill me-1" style="color:#7F1D3F;"></i>Mapa centrado en <strong>${cleanMun}</strong>. Arrastra el marcador azul en el mapa hasta tu ubicación exacta.</span>`;
                }
            }
        } catch (err) {
            console.error('Error buscando dirección:', err);
            const fallbackCoords = findKnownCityCoords(selectedDept, rawMun) || geo.departments[selectedDept]?.center;
            if (fallbackCoords) {
                marker.setLatLng(fallbackCoords);
                map.flyTo(fallbackCoords, 14, { duration: 1.0 });
                updateFormCoords(fallbackCoords[0], fallbackCoords[1]);
            }
            if (statusDisplay) {
                statusDisplay.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>No se pudo conectar al buscador. Ubica tu local arrastrando el pin azul en el mapa.</span>';
            }
        }
    }

    // 6. Eventos de interacción en Mapa y Marcador (Punto Azul)
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateFormCoords(e.latlng.lat, e.latlng.lng);
        reverseGeocode(e.latlng.lat, e.latlng.lng, true);
    });

    marker.on('dragend', function() {
        const pos = marker.getLatLng();
        updateFormCoords(pos.lat, pos.lng);
        reverseGeocode(pos.lat, pos.lng, true);
    });

    // 7. Eventos de los Selectores de Departamento y Ciudad
    stateSelect?.addEventListener('change', function() {
        currentState = this.value;
        populateCities(currentState, null);
        const deptObj = geo.departments[currentState];
        if (deptObj) {
            const firstCityCoords = deptObj.cities[citySelect.value] || deptObj.center;
            marker.setLatLng(firstCityCoords);
            map.flyTo(firstCityCoords, deptObj.zoom || 13, { duration: 1.0 });
            updateFormCoords(firstCityCoords[0], firstCityCoords[1]);
            reverseGeocode(firstCityCoords[0], firstCityCoords[1]);
        }
    });

    citySelect?.addEventListener('change', function() {
        if (this.value === '_other_') {
            const customCity = prompt('Escribe el nombre del municipio o alcaldía:');
            if (customCity && customCity.trim()) {
                const clean = normalizeMunicipality(customCity.trim(), currentState);
                const opt = document.createElement('option');
                opt.value = clean;
                opt.textContent = clean;
                opt.selected = true;
                citySelect.insertBefore(opt, citySelect.lastElementChild);
                currentCity = clean;
                searchAddress();
            } else {
                citySelect.selectedIndex = 0;
            }
            return;
        }

        currentCity = this.value;
        const cityCoords = findKnownCityCoords(currentState, currentCity);

        if (cityCoords && !(addressInput && addressInput.value.trim().length >= 4)) {
            marker.setLatLng(cityCoords);
            map.flyTo(cityCoords, 15, { duration: 1.0 });
            updateFormCoords(cityCoords[0], cityCoords[1]);
            reverseGeocode(cityCoords[0], cityCoords[1], true);
        } else {
            searchAddress();
        }
    });

    // Evento en Código Postal para centrar o refinar búsqueda automáticamente
    postalCodeInput?.addEventListener('change', function() {
        if (this.value.trim().length >= 4) {
            searchAddress();
        }
    });
    postalCodeInput?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchAddress();
        }
    });

    // 8. Evento del Input de Dirección y Botón de Búsqueda (Bidireccional)
    let addressDebounceTimer = null;
    addressInput?.addEventListener('input', function() {
        const val = this.value.trim();
        if (statusDisplay) {
            if (val) {
                statusDisplay.innerHTML = `<span class="text-dark"><i class="bi bi-geo-alt-fill text-danger me-1"></i> ${val}</span> <span class="text-muted text-2xs d-block mt-0.5">Presiona "Ubicar" para mover el mapa a esta dirección.</span>`;
            } else {
                statusDisplay.innerHTML = `<span class="text-muted">Escribe tu dirección o mueve el punto en el mapa.</span>`;
            }
        }
        clearTimeout(addressDebounceTimer);
        if (val.length >= 6) {
            addressDebounceTimer = setTimeout(() => {
                searchAddress();
            }, 1100);
        }
    });

    addressInput?.addEventListener('change', function() {
        clearTimeout(addressDebounceTimer);
        if (this.value.trim().length >= 4) {
            searchAddress();
        }
    });

    searchBtn?.addEventListener('click', function() {
        clearTimeout(addressDebounceTimer);
        searchAddress();
    });

    addressInput?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(addressDebounceTimer);
            searchAddress();
        }
    });

    // 9. Evento de GPS del Navegador
    gpsBtn?.addEventListener('click', function() {
        if (!navigator.geolocation) {
            if (statusDisplay) {
                statusDisplay.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-circle me-1"></i> Tu navegador no soporta geolocalización GPS. Puedes usar el buscador o hacer clic en el mapa.</span>';
            }
            return;
        }
        if (statusDisplay) {
            statusDisplay.innerHTML = '<span class="text-primary"><i class="bi bi-crosshair spin-me me-1"></i> Solicitando coordenadas satelitales GPS...</span>';
        }
        navigator.geolocation.getCurrentPosition(
            pos => {
                const lat = pos.coords.latitude;
                const lon = pos.coords.longitude;
                marker.setLatLng([lat, lon]);
                map.flyTo([lat, lon], 17, { duration: 1.2 });
                updateFormCoords(lat, lon);
                reverseGeocode(lat, lon);
            },
            err => {
                let msg = 'No se pudo obtener la ubicación GPS.';
                if (err.code === 1) {
                    msg = 'Permiso de ubicación no concedido en el navegador. Puedes usar el buscador o hacer clic en el mapa.';
                } else if (err.code === 2) {
                    msg = 'Ubicación no disponible en este momento. Usa el buscador o haz clic en el mapa.';
                } else if (err.code === 3) {
                    msg = 'Tiempo de espera de GPS agotado. Usa el buscador o haz clic en el mapa.';
                }
                if (statusDisplay) {
                    statusDisplay.innerHTML = `<span class="text-warning"><i class="bi bi-info-circle me-1"></i> ${msg}</span>`;
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
        );
    });

    // Invalidar tamaño de mapa tras render inicial para evitar tiles grises
    setTimeout(() => { map.invalidateSize(); }, 400);

    return { map, marker };
};

