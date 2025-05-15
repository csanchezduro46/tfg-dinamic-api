import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class PasswordService {
    constructor(private readonly http: HttpClient) { }

    forgotPassword(email: string): Observable<any> {
        return this.http.post('/api/password/forgot', { email });
    }

    resetPassword(data: any): Observable<any> {
        return this.http.post('/api/password/reset', data);
    }
}
