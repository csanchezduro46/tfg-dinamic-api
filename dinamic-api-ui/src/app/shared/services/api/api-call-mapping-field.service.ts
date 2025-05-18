import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { catchError, Observable } from 'rxjs';
import { environment } from '../../../../environments/environment';
import { ErrorHandlerService } from '../errors/error-handler.service';

@Injectable({ providedIn: 'root' })
export class ApiCallMappingFieldService {
    private readonly baseUrl = environment.apiUrl;

    constructor(private readonly http: HttpClient, private readonly errorHandler: ErrorHandlerService) { }

    getByMapping(mappingId: number): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/mappings/${mappingId}/fields`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    create(mappingId: number, data: any): Observable<any> {
        return this.http.post(`${this.baseUrl}/api/mappings/${mappingId}/fields`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    delete(mappingId: number, fieldId: number): Observable<any> {
        return this.http.delete(`${this.baseUrl}/api/mappings/${mappingId}/fields/${fieldId}`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }
}
