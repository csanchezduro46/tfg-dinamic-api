import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { Observable } from "rxjs";

@Injectable({ providedIn: 'root' })
export class DatabaseConnectionService {
    constructor(private readonly http: HttpClient) { }

    getAll(): Observable<any> {
        return this.http.get('/api/db-connections');
    }

    getMine(): Observable<any> {
        return this.http.get('/api/db-connections/me');
    }

    get(id: number): Observable<any> {
        return this.http.get(`/api/db-connections/${id}`);
    }

    create(data: any): Observable<any> {
        return this.http.post('/api/db-connections', data);
    }

    update(id: number, data: any): Observable<any> {
        return this.http.put(`/api/db-connections/${id}`, data);
    }

    delete(id: number): Observable<any> {
        return this.http.delete(`/api/db-connections/${id}`);
    }

    test(id: number): Observable<any> {
        return this.http.get(`/api/db-connections/${id}/test`);
    }
}
