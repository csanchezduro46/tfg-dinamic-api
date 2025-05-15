import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class ApiCallMappingService {
    constructor(private readonly http: HttpClient) { }

    getAll(): Observable<any> {
        return this.http.get('/api/mappings');
    }

    getMine(): Observable<any> {
        return this.http.get('/api/mappings/me');
    }

    get(id: number): Observable<any> {
        return this.http.get(`/api/mappings/${id}`);
    }

    create(data: any): Observable<any> {
        return this.http.post('/api/mappings', data);
    }

    update(id: number, data: any): Observable<any> {
        return this.http.put(`/api/mappings/${id}`, data);
    }

    delete(id: number): Observable<any> {
        return this.http.delete(`/api/mappings/${id}`);
    }
}