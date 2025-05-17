import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, catchError, map, Observable } from 'rxjs';
import { ErrorHandlerService } from '../errors/error-handler.service';
import { environment } from '../../../../environments/environment';
import { User } from '../../../core/models/user.model';

@Injectable({ providedIn: 'root' })
export class AuthService {
    private readonly baseUrl = environment.apiUrl;
    private user: any = null;
    private readonly userObject = new BehaviorSubject<User | null>(null);
    user$ = this.userObject.asObservable();

    constructor(private readonly http: HttpClient, private readonly errorHandler: ErrorHandlerService) { }

    /**
   * Guarda los datos del usuario autenticado
   * @param userData objeto devuelto por /api/user
   */
    setUser(userData: any): void {
        this.userObject.next(userData);
        this.user = userData;
        // También puedes persistirlo si lo necesitas:
        localStorage.setItem('auth_user', JSON.stringify(userData));
    }

    /**
     * Devuelve el usuario actual en memoria
     */
    getUser(): any {
        return this.user ?? JSON.parse(localStorage.getItem('auth_user') ?? 'null');
    }

    login(data: { email: string; password: string }): Observable<any> {
        return this.http.post(`${this.baseUrl}/oauth/login`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    register(data: any): Observable<any> {
        return this.http.post(`${this.baseUrl}/oauth/signup`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );

    }

    logout(): Observable<any> {
        this.user = null;
        this.userObject.next(null);
        localStorage.removeItem('auth_user');
        localStorage.removeItem('token'); // si lo guardas como token
        localStorage.clear(); // opcional, si no guardas nada más

        return this.http.get(`${this.baseUrl}/oauth/logout`).pipe(
            // catchError(err => this.errorHandler.handle(err))
        );
    }

    getMe(): Observable<any> {
        return this.http.get(`${this.baseUrl}/oauth/me`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    hasRole(role: string): boolean {
        const roles = this.getUser()?.roles;
      
        if (!roles) return false;
      
        if (Array.isArray(roles)) {
          if (typeof roles[0] === 'string') {
            return roles.includes(role);
          }
      
          if (typeof roles[0] === 'object') {
            return roles.some(r => r.name === role);
          }
        }
      
        return false;
      }
      
}
