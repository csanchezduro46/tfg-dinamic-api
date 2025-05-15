import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({ providedIn: 'root' })
export class PlatformService {
    constructor(private readonly http: HttpClient) { }

    getAll() {
        return this.http.get('/api/platforms');
    }

    create(data: any) {
        return this.http.post('/api/platforms', data);
    }

    update(id: number, data: any) {
        return this.http.put(`/api/platforms/${id}`, data);
    }

    delete(id: number) {
        return this.http.delete(`/api/platforms/${id}`);
    }
}
