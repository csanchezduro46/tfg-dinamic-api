import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class VerificationService {
    constructor(private readonly http: HttpClient) { }

    sendEmailVerification(): Observable<any> {
        return this.http.post('/api/oauth/email/verification-notification', {});
    }

    verifyEmail(id: string, hash: string): Observable<any> {
        return this.http.get(`/api/oauth/email/verify/${id}/${hash}`);
    }
}
