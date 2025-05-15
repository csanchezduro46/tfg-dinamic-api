import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({ providedIn: 'root' })
export class PlatformConnectionCredentialsService {
    constructor(private readonly http: HttpClient) { }

    get(id: number) {
        return this.http.get(`/api/connections/${id}/credentials`);
    }

    store(id: number, data: any) {
        return this.http.post(`/api/connections/${id}/credentials`, data);
    }

    update(id: number, data: any) {
        return this.http.put(`/api/connections/${id}/credentials`, data);
    }

    delete(id: number) {
        return this.http.delete(`/api/connections/${id}/credentials`);
    }

    deleteOne(id: number, keyId: number) {
        return this.http.delete(`/api/connections/${id}/credentials/${keyId}`);
    }

    test(id: number) {
        return this.http.get(`/api/connections/${id}/test`);
    }
}
