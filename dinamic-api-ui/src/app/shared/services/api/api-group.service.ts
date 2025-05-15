import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { Observable } from "rxjs";

@Injectable({ providedIn: 'root' })
export class ApiGroupService {
    constructor(private readonly http: HttpClient) { }

    getAll(): Observable<any> {
        return this.http.get('/api/groups');
    }

    create(data: any): Observable<any> {
        return this.http.post('/api/groups', data);
    }

    update(id: number, data: any): Observable<any> {
        return this.http.put(`/api/groups/${id}`, data);
    }

    delete(id: number): Observable<any> {
        return this.http.delete(`/api/groups/${id}`);
    }
}
