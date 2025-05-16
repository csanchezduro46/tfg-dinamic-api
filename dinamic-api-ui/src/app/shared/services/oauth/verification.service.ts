import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { catchError, Observable } from 'rxjs';
import { environment } from '../../../../environments/environment';
import { ErrorHandlerService } from '../errors/error-handler.service';

@Injectable({ providedIn: 'root' })
export class VerificationService {
    private readonly baseUrl = environment.apiUrl;

    constructor(private readonly http: HttpClient, private readonly errorHandler: ErrorHandlerService) { }

    sendEmailVerification(email: string): Observable<any> {
        return this.http.post(`${this.baseUrl}/oauth/email/verification-resend`, { email }).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    verifyEmail(id: string, hash: string, expires: string, signature: string): Observable<any> {
        const url = `${this.baseUrl}/oauth/email/verify/${id}/${hash}?expires=${expires}&signature=${signature}`;
        return this.http.get(url).pipe(
            catchError(err => this.errorHandler.handle(err))
        );

    }
}
