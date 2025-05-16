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
        path: 'dashboard',
        loadComponent: () => import('./pages/dashboard/dashboard-page/dashboard-page.component').then(m => m.DashboardPageComponent),
        title: 'Dashboard'
      },
      {
        path: 'api-rest',
        loadComponent: () => import('./pages/api-rest/api-rest-page/api-rest-page.component').then(m => m.ApiRestPageComponent),
        title: 'API Rest'
      },
      {
        path: 'external-integrations',
        loadComponent: () => import('./pages/external-integrations/external-integrations-page/external-integrations-page.component').then(m => m.ExternalIntegrationsPageComponent),
        title: 'Integraciones'
      },
      {
        path: 'database-schema',
        loadComponent: () => import('./pages/database-schema/database-schema-page/database-schema-page.component').then(m => m.DatabaseSchemaPageComponent),
        title: 'Esquemas'
      },
      {
        path: '',
        redirectTo: 'dashboard',
        pathMatch: 'full',
      }
    ]
  },

  { path: '**', redirectTo: 'login' }
];