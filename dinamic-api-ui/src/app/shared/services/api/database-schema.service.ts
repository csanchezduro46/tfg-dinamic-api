import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { catchError, Observable } from "rxjs";
import { environment } from "../../../../environments/environment";
import { ErrorHandlerService } from "../errors/error-handler.service";

@Injectable({ providedIn: 'root' })
export class DatabaseSchemaService {
    private readonly baseUrl = environment.apiUrl;

    constructor(private readonly http: HttpClient, private readonly errorHandler: ErrorHandlerService) { }

    getTables(id: number): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/db-connections/${id}/tables`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    getColumns(id: number, table: string): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/db-connections/${id}/tables/${table}/columns`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    getSchema(id: number): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/db-connections/${id}/schema`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }
}
