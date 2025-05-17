import { Injectable } from '@angular/core';
import {
  HttpEvent, HttpInterceptor, HttpHandler, HttpRequest, HttpErrorResponse
} from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { AuthService } from '../../shared/services/oauth/auth.service';
import { Router } from '@angular/router';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  private readonly excludedUrls = [
    '/oauth/login',
    '/oauth/signup',
    '/oauth/password/forgot',
    '/oauth/email/verify',
    '/oauth/email/verification-resend'
  ];

  constructor(
    private readonly auth: AuthService,
    private readonly router: Router
  ) { }

  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    const token = localStorage.getItem('token');
    const shouldExclude = this.excludedUrls.some(path => req.url.includes(path));

    if (!token && !shouldExclude) {
      return this.handleUnauthorized('No hay token');
    }

    if (token && !shouldExclude) {
      const authReq = req.clone({
        setHeaders: { Authorization: `Bearer ${token}` }
      });
      if (!this.auth.getUser()) {
        return this.handleUnauthorized('Usuario no autenticado');
      }

      return next.handle(authReq).pipe(
        catchError((error: HttpErrorResponse) => this.handleError(error))
      );
    }

    return next.handle(req).pipe(
      catchError((error: HttpErrorResponse) => this.handleError(error))
    );
  }

  /**
   * Centraliza errores 401/403
   */
  private handleError(error: HttpErrorResponse): Observable<never> {
    if (error.status === 401 || error.status === 403) {
      return this.handleUnauthorized();
    }
    return throwError(() => error);
  }

  /**
   * Logout, limpieza y redirección al login
   */
  private handleUnauthorized(reason = 'No autorizado'): Observable<never> {
    this.auth.logout(); // ya borra localStorage
    localStorage.clear(); // por si acaso
    this.router.navigate(['/login']);
    return throwError(() => new Error(reason));
  }
}
