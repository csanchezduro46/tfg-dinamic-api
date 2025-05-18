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
      { label: 'Plataformas', path: '/platforms/list' },
      { label: 'Versiones', path: '/platforms/versions' },
      { label: 'APIs', path: '/platforms/apis' }
    ]
  },
  {
    label: 'Integraciones',
    icon: ['fal', 'layer-group'],
    children: [
      { label: 'Plataformas externas', path: '/external-integrations' },
      { label: 'BBDD', path: '/databases/list' },
      { label: 'Sincronizaciones', path: '/connections/mappings' },
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
