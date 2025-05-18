import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { catchError, Observable } from "rxjs";
import { environment } from "../../../../environments/environment";
import { ErrorHandlerService } from "../errors/error-handler.service";

@Injectable({ providedIn: 'root' })
export class DatabaseConnectionService {
    private readonly baseUrl = environment.apiUrl;

    constructor(private readonly http: HttpClient, private readonly errorHandler: ErrorHandlerService) { }

    getAll(): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/db-connections`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    getMine(): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/db-connections/me`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    get(id: number): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/db-connections/${id}`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    getTables(id: number): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/db-connections/${id}/tables`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    getTableColumns(id: number, table: string): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/db-connections/${id}/tables/${table}/columns`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    create(data: any): Observable<any> {
        return this.http.post(`${this.baseUrl}/api/db-connections`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    update(id: number, data: any): Observable<any> {
        return this.http.put(`${this.baseUrl}/api/db-connections/${id}`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    delete(id: number): Observable<any> {
        return this.http.delete(`${this.baseUrl}/api/db-connections/${id}`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    test(id: number): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/db-connections/${id}/test`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }
}
