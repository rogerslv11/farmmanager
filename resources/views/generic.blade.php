@extends('layouts.app')
@section('title', $title ?? 'FarmManager')
@section('content')
@php
  $meta = [
    'properties' => ['Propriedades', 'Cadastre e acompanhe cada fazenda, sítio e área produtiva.', 'home'],
    'fields' => ['Talhões', 'Divida as propriedades em talhões e acompanhe a cultura ativa.', 'layers'],
    'crops' => ['Culturas', 'Catálogo de culturas com ciclo e produtividade esperada.', 'sun'],
    'plantings' => ['Plantações', 'Do planejamento à colheita em cada talhão.', 'sprout'],
    'seasons' => ['Safras', 'Planeje safras e acompanhe progresso, produção e margem.', 'calendar'],
    'season-dashboard' => [$season->name ?? 'Safra', ($season->crop?->name ?? '').' · '.($season->property?->name ?? ''), 'calendar'],
    'activities' => ['Atividades agrícolas', 'Calendário operacional: plantio, manejo, pulverização e colheita.', 'clipboard'],
    'production' => ['Produção', 'Registros de produção e produtividade por área.', 'package'],
    'harvests' => ['Colheitas', 'Colheitas registradas com perdas e qualidade.', 'truck'],
    'inventory' => ['Estoque', 'Insumos e materiais com movimentações e alerta de mínimo.', 'database'],
    'purchases' => ['Compras', 'Pedidos a fornecedores com entrada automática no estoque.', 'cart'],
    'suppliers' => ['Fornecedores', 'Parceiros de insumos, máquinas e serviços.', 'briefcase'],
    'equipment' => ['Máquinas e equipamentos', 'Frota, horímetro e disponibilidade operacional.', 'tool'],
    'maintenance' => ['Manutenções', 'Preventivas e corretivas com alertas de vencimento.', 'sliders'],
    'employees' => ['Funcionários', 'Equipe, cargos, alocação e atividades atribuídas.', 'users'],
    'customers' => ['Clientes', 'Compradores da produção e histórico de receita.', 'user'],
    'sales' => ['Vendas', 'Comercialização da produção por lote e cultura.', 'trend'],
    'expenses' => ['Custos e despesas', 'Lançamentos operacionais vinculados a safra e talhão.', 'wallet'],
    'reports' => ['Relatórios', 'Produção, estoque e financeiro com filtros por período.', 'chart'],
    'audit' => ['Auditoria', 'Rastro completo de quem fez o quê no sistema.', 'shield'],
  ];
  [$mTitle, $mSubtitle, $mIcon] = $meta[$component ?? ''] ?? [$title ?? '', '', 'grid'];
@endphp
<x-page-header :title="$mTitle" :subtitle="$mSubtitle" :icon="$mIcon" />
@switch($component ?? '')
  @case('properties') @livewire('properties.property-index') @break
  @case('fields') @livewire('fields.field-index') @break
  @case('crops') @livewire('crops.crop-index') @break
  @case('plantings') @livewire('plantings.planting-index') @break
  @case('seasons') @livewire('seasons.season-index') @break
  @case('season-dashboard') @livewire('seasons.season-dashboard', ['season' => $season]) @break
  @case('activities') @livewire('activities.activity-index') @break
  @case('production') @livewire('production.production-index') @break
  @case('harvests') @livewire('harvests.harvest-index') @break
  @case('inventory') @livewire('inventory.inventory-index') @break
  @case('purchases') @livewire('purchases.purchase-index') @break
  @case('suppliers') @livewire('suppliers.supplier-index') @break
  @case('equipment') @livewire('equipment.equipment-index') @break
  @case('maintenance') @livewire('maintenance.maintenance-index') @break
  @case('employees') @livewire('employees.employee-index') @break
  @case('customers') @livewire('customers.customer-index') @break
  @case('sales') @livewire('sales.sale-index') @break
  @case('expenses') @livewire('expenses.expense-index') @break
  @case('reports') @livewire('reports.reports-index') @break
  @case('audit') @livewire('audit.audit-index') @break
@endswitch
@endsection
