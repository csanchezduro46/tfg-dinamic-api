import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class ApiCallMappingFieldService {
    constructor(private readonly http: HttpClient) { }

    getByMapping(mappingId: number): Observable<any> {
        return this.http.get(`/api/mappings/${mappingId}/fields`);
    }

    create(mappingId: number, data: any): Observable<any> {
        return this.http.post(`/api/mappings/${mappingId}/fields`, data);
    }

    delete(mappingId: number, fieldId: number): Observable<any> {
        return this.http.delete(`/api/mappings/${mappingId}/fields/${fieldId}`);
    }
}
