import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { Observable } from "rxjs";

@Injectable({ providedIn: 'root' })
export class ApiCallService {
    constructor(private readonly http: HttpClient) { }

    getByVersion(versionId: number): Observable<any> {
        return this.http.get(`/api/calls/version/${versionId}`);
    }

    get(id: number): Observable<any> {
        return this.http.get(`/api/calls/${id}`);
    }

    create(versionId: number, data: any): Observable<any> {
        return this.http.post(`/api/calls/version/${versionId}`, data);
    }

    update(id: number, data: any): Observable<any> {
        return this.http.put(`/api/calls/${id}`, data);
    }

    delete(id: number): Observable<any> {
        return this.http.delete(`/api/calls/${id}`);
    }
}
