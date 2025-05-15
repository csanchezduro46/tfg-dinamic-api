import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({ providedIn: 'root' })
export class PlatformVersionService {
    constructor(private readonly http: HttpClient) { }

    getAll() {
        return this.http.get('/api/versions');
    }

    getByPlatform(platformId: number) {
        return this.http.get(`/api/platforms/${platformId}/versions`);
    }

    create(platformId: number, data: any) {
        return this.http.post(`/api/platforms/${platformId}/versions`, data);
    }

    update(id: number, data: any) {
        return this.http.put(`/api/versions/${id}`, data);
    }

    delete(id: number) {
        return this.http.delete(`/api/versions/${id}`);
    }
}
