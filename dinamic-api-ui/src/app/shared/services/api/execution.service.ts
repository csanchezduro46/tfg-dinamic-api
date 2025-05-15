import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({ providedIn: 'root' })
export class ExecutionService {
    constructor(private readonly http: HttpClient) { }

    getAll() {
        return this.http.get('/api/executions');
    }

    getByMapping(id: number) {
        return this.http.get(`/api/executions/mapping/${id}`);
    }

    create(mappingId: number, data: any) {
        return this.http.post(`/api/executions/mapping/${mappingId}/execute`, data);
    }

    update(id: number, data: any) {
        return this.http.put(`/api/executions/${id}`, data);
    }

    delete(id: number) {
        return this.http.delete(`/api/executions/${id}`);
    }

    launch(id: number) {
        return this.http.post(`/api/executions/${id}/launch`, {});
    }
}
