import { IconProp } from '@fortawesome/fontawesome-svg-core';

export interface NavItem {
  label: string;
  path: string;
  icon?: IconProp;
}

export interface NavGroup {
  label: string;
  icon?: IconProp;
  children: NavItem[];
}

export const NAV_ITEMS: NavGroup[] = [
  {
    label: 'Plataformas externas',
    icon: ['fal', 'plug'],
    children: [
      { label: 'Mis plataformas', path: '/external-integrations' },
      { label: 'Plataformas', path: '/platforms/list' },
      { label: 'Versiones', path: '/platforms/versions' },
      { label: 'APIs', path: '/platforms/apis' }
    ]
  },
  {
    label: 'BBDD',
    icon: ['fal', 'database'],
    children: [
      { label: 'Mis BBDD', path: '/databases/list' },
      { label: 'Esquemas', path: '/databases/schemas' }
    ]
  },
  {
    label: 'Conexiones',
    icon: ['fal', 'server'],
    children: [
      { label: 'Conexiones', path: '/connections/mappings' },
      { label: 'Mapeos', path: '/connections/fields' }
    ]
  },
  {
    label: 'Ejecuciones',
    icon: ['fal', 'history'],
    children: [
      { label: 'Histórico', path: '/executions/history' },
      { label: 'Programadas', path: '/executions/scheduled' }
    ]
  }
];
