import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { catchError, Observable } from 'rxjs';
import { environment } from '../../../../environments/environment';
import { ErrorHandlerService } from '../errors/error-handler.service';

@Injectable({ providedIn: 'root' })
export class PasswordService {
    private readonly baseUrl = environment.apiUrl;

    constructor(private readonly http: HttpClient, private readonly errorHandler: ErrorHandlerService) { }

    forgotPassword(email: string): Observable<any> {
        return this.http.post(`${this.baseUrl}/oauth/password/forgot`, { email }).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    resetPassword(data: any): Observable<any> {
        return this.http.post(`${this.baseUrl}/oauth/password/reset`, data, { withCredentials: true }).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }
}
