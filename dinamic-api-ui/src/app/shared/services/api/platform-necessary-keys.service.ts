import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({ providedIn: 'root' })
export class PlatformNecessaryKeysService {
    constructor(private readonly http: HttpClient) { }

    get(platformId: number) {
        return this.http.get(`/api/platforms/${platformId}/necessary-keys`);
    }

    create(platformId: number, data: any) {
        return this.http.post(`/api/platforms/${platformId}/necessary-keys`, data);
    }

    update(platformId: number, keyId: number, data: any) {
        return this.http.put(`/api/platforms/${platformId}/necessary-keys/${keyId}`, data);
    }

    delete(platformId: number, keyId: number) {
        return this.http.delete(`/api/platforms/${platformId}/necessary-keys/${keyId}`);
    }
}
