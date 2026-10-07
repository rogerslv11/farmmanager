# FarmManager — SaaS de Gestão Rural (TALL Stack)

Plataforma completa para gestão de propriedades rurais: propriedades, talhões, culturas,
plantações, safras, atividades, produção, colheitas, estoque, compras, fornecedores,
máquinas, manutenções, funcionários, clientes, vendas, custos, relatórios, notificações,
busca global (Ctrl+K), auditoria e controle de acesso por perfil — tudo multi-tenant por organização.

## Stack

Laravel 12 · Livewire 4 · Alpine.js · Tailwind CSS 3 · Chart.js · Sanctum · Queues · Notifications

## Requisitos

- PHP 8.2+ (extensões padrão do Laravel)
- Composer · Node 20+ · NPM
- **Produção:** MySQL 8+ · **Dev/teste:** SQLite (padrão, zero-config)

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate

# Desenvolvimento (SQLite, já vem pronto)
touch database/database.sqlite
php artisan migrate --seed

# Produção (MySQL) — ajuste o .env:
# DB_CONNECTION=mysql DB_HOST=... DB_DATABASE=farmmanager ...
php artisan migrate --force

npm install && npm run build   # ou npm run dev
php artisan serve
```

## Acesso demo (seed)

| Perfil | E-mail | Senha |
|---|---|---|
| Admin | admin@fazenda.com | password |
| Gestor | gestor@fazenda.com | password |
| Agrônomo | agro@fazenda.com | password |
| Estoquista | estoque@fazenda.com | password |
| Funcionário | funcionario@fazenda.com | password |

## Arquitetura

```
app/
├── Enums/        # Status e tipos (PlantingStatus, SeasonStatus, ...)
├── Traits/       # BelongsToOrganization (isolamento multi-tenant)
├── Scopes/       # OrganizationScope (global scope automático)
├── Support/      # CurrentOrganization (sessão)
├── Services/     # InventoryService, PurchaseService, ProductionService, DashboardService, AuditService
├── Policies/     # ModulePolicy + User::canAccess() por perfil
├── Jobs/         # CheckStockAlerts, CheckOperationalAlerts
├── Notifications/# LowStockAlert, GenericFarmAlert
├── Livewire/     # 1 componente por domínio + Dashboard, GlobalSearch, Bell, Reports, Audit
└── Http/         # AuthController, EnsureModuleAccess
```

Regras críticas (testadas em `tests/Feature`):
- Entrada ↑ / saída ↓ estoque; saída maior que o saldo é rejeitada.
- Compra `recebida` dá entrada automática no estoque.
- Colheita atualiza `realized_yield` da plantação e gera produção.
- Global scope impede vazamento entre organizações.

## Filas e agendador

```bash
php artisan queue:work
php artisan schedule:work   # dispara CheckStockAlerts + CheckOperationalAlerts
```

## Testes

```bash
php artisan test
```
