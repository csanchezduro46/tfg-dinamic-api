import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { catchError, Observable } from "rxjs";
import { environment } from "../../../../environments/environment";
import { ErrorHandlerService } from "../errors/error-handler.service";

@Injectable({ providedIn: 'root' })
export class ApiCallService {
    private readonly baseUrl = environment.apiUrl;

    constructor(private readonly http: HttpClient, private readonly errorHandler: ErrorHandlerService) { }

    getAll(): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/api-calls`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    getByVersion(versionId: number): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/api-calls/platform-version/${versionId}`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    get(id: number): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/api-calls/${id}`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    getFields(id: number): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/api-calls/${id}/fields`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    create(data: any): Observable<any> {
        return this.http.post(`${this.baseUrl}/api/api-calls`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    update(id: number, data: any): Observable<any> {
        return this.http.put(`${this.baseUrl}/api/api-calls/${id}`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    delete(id: number): Observable<any> {
        return this.http.delete(`${this.baseUrl}/api/api-calls/${id}`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }
}
