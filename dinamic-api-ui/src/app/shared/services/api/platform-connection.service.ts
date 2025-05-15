import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({ providedIn: 'root' })
export class PlatformConnectionService {
    constructor(private readonly http: HttpClient) { }

    getAll() {
        return this.http.get('/api/connections');
    }

    getMine() {
        return this.http.get('/api/connections/me');
    }

    get(id: number) {
        return this.http.get(`/api/connections/${id}`);
    }

    create(data: any) {
        return this.http.post('/api/connections', data);
    }

    update(id: number, data: any) {
        return this.http.put(`/api/connections/${id}`, data);
    }

    delete(id: number) {
        return this.http.delete(`/api/connections/${id}`);
    }
}
