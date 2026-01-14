## Backend Inmobiliaria

Proyecto Laravel con arquitectura basada en servicios.

### Arquitectura
- Controllers delgados
- Lógica en Services
- Acceso a datos mediante Repositories
- Uso de Interfaces (IService / IRepository)

### Estructura
- app/Http/Controllers
- app/Http/Requests
- app/Services
- app/Repositories
- app/Validation
- app/Models

### Convenciones
- Controllers solo orquestan peticiones
- Services contienen la lógica de negocio
- Repositories encapsulan Eloquent
- Las interfaces siempre se implementan en carpeta Implements

