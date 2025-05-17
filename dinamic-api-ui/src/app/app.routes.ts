import { Routes } from '@angular/router';
import { authGuard } from './core/guards/auth.guard';
import { verifiedGuard } from './core/guards/verified.guard';
import { LayoutComponent } from './shared/layout/layout.component';


export const routes: Routes = [
  {
    path: 'login',
    loadComponent: () =>
      import('./pages/oauth/login/login.component').then(m => m.LoginComponent),
    title: 'Iniciar sesión',
  },
  {
    path: 'register',
    loadComponent: () =>
      import('./pages/oauth/register/register.component').then(m => m.RegisterComponent),
    title: 'Registro',
  },
  {
    path: 'reset-password',
    loadComponent: () =>
      import('./pages/oauth/reset-password/reset-password.component').then(m => m.ResetPasswordComponent),
    title: 'Nueva contraseña',
  },
  {
    path: '',
    canActivate: [authGuard],
    component: LayoutComponent,
    children: [
      {
        path: 'profile',
        loadComponent: () => import('./pages/oauth/profile/profile.component').then(m => m.ProfileComponent),
        title: 'Perfil'
      },
      {
        path: 'dashboard',
        loadComponent: () => import('./pages/dashboard/dashboard-page/dashboard-page.component').then(m => m.DashboardPageComponent),
        title: 'Dashboard'
      },
      // 🧩 Plataformas
      {
        path: 'external-integrations',
        children: [
          {
            path: '',
            loadComponent: () =>
              import('./pages/external-integrations/external-integrations-page/external-integrations-page.component').then(m => m.ExternalIntegrationsPageComponent),
            title: 'Conexiones externas'
          },
          {
            path: 'create',
            loadComponent: () =>
              import('./pages/external-integrations/external-integration-create-page/external-integration-create-page.component').then(m => m.ExternalIntegrationCreatePage),
            title: 'Formulario'
          },
          {
            path: 'edit/:id',
            loadComponent: () =>
              import('./pages/external-integrations/external-integration-create-page/external-integration-create-page.component').then(m => m.ExternalIntegrationCreatePage),
            title: 'Formulario'
          }
        ]
      },
      {
        path: 'platforms',
        title: 'Plataformas',
        children: [
          {
            path: '',
            redirectTo: 'list',
            pathMatch: 'full'
          },
          {
            path: 'list',
            loadComponent: () =>
              import('./pages/platforms/platforms-page/platforms-page.component').then(m => m.PlatformsPageComponent),
            title: 'Plataformas'
          },
          {
            path: 'versions',
            loadComponent: () =>
              import('./pages/platforms/platform-versions-page/platform-versions-page.component').then(m => m.PlatformVersionsPageComponent),
            title: 'Versiones'
          },
          {
            path: 'apis',
            loadComponent: () =>
              import('./pages/platforms/platform-apis-page/platform-apis-page.component').then(m => m.PlatformApisPageComponent),
            title: 'APIs'
          }
        ]
      },

      // 🧩 BBDD
      {
        path: 'databases',
        title: 'Mis BBDD',
        children: [
          {
            path: '',
            redirectTo: 'list',
            pathMatch: 'full'
          },
          {
            path: 'list',
            loadComponent: () =>
              import('./pages/databases/databases-list-page/databases-list-page.component').then(m => m.DatabasesListPageComponent),
            title: 'Mis BBDD'
          },
        ]
      },

      // 🧩 Conexiones
      {
        path: 'connections',
        title: 'Conexiones',
        children: [
          {
            path: '',
            redirectTo: 'mappings',
            pathMatch: 'full'
          },
          {
            path: 'mappings',
            loadComponent: () =>
              import('./pages/call-mappings/call-mappings-page/call-mappings-page.component').then(m => m.CallMappingsPageComponent),
            title: 'Conexiones'
          },
          {
            path: 'mappings/fields/:id',
            loadComponent: () =>
              import('./pages/call-mappings/call-mapping-fields-page/call-mapping-fields-page.component').then(m => m.CallMappingFieldsPageComponent),
            title: 'Campos del mapeo'
          }

        ]
      },

      // 🧩 Ejecuciones
      {
        path: 'executions',
        title: 'Ejecuciones',
        children: [
          {
            path: '',
            redirectTo: 'history',
            pathMatch: 'full'
          },
          {
            path: 'history',
            loadComponent: () =>
              import('./pages/executions/executions-page/executions-page.component').then(m => m.ExecutionsPageComponent),
            title: 'Histórico'
          },
          {
            path: 'scheduled',
            loadComponent: () =>
              import('./pages/executions/scheduled-executions-page/scheduled-executions-page.component').then(m => m.ScheduledExecutionsPageComponent),
            title: 'Ejecuciones programadas'
          }
        ]
      },
      { path: '**', redirectTo: 'dashboard' }
    ]
  },

  { path: '**', redirectTo: 'login' }
];