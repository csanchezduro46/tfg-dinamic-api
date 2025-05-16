import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { catchError, map, Observable } from 'rxjs';
import { ErrorHandlerService } from '../errors/error-handler.service';
import { environment } from '../../../../environments/environment';

@Injectable({ providedIn: 'root' })
export class AuthService {
    private readonly baseUrl = environment.apiUrl;

    constructor(private readonly http: HttpClient, private readonly errorHandler: ErrorHandlerService) { }

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
        return this.http.get(`${this.baseUrl}/oauth/logout`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    getMe(): Observable<any> {
        return this.http.get(`${this.baseUrl}/oauth/me`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }
}
