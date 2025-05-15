import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class AuthService {
    constructor(private readonly http: HttpClient) { }

    login(data: { email: string; password: string }): Observable<any> {
        return this.http.post('/api/oauth/login', data);
    }

    register(data: any): Observable<any> {
        return this.http.post('/api/signup', data);
    }

    logout(): Observable<any> {
        return this.http.get('/api/oauth/logout');
    }

    getMe(): Observable<any> {
        return this.http.get('/api/oauth/me');
    }
}
