import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { Observable } from "rxjs";

@Injectable({ providedIn: 'root' })
export class DatabaseSchemaService {
    constructor(private readonly http: HttpClient) { }

    getTables(id: number): Observable<any> {
        return this.http.get(`/api/db-connections/${id}/tables`);
    }

    getColumns(id: number, table: string): Observable<any> {
        return this.http.get(`/api/db-connections/${id}/tables/${table}/columns`);
    }

    getSchema(id: number): Observable<any> {
        return this.http.get(`/api/db-connections/${id}/schema`);
    }
}
