import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { catchError } from 'rxjs';
import { environment } from '../../../../environments/environment';
import { ErrorHandlerService } from '../errors/error-handler.service';

@Injectable({ providedIn: 'root' })
export class PlatformVersionService {
    private readonly baseUrl = environment.apiUrl;

    constructor(private readonly http: HttpClient, private readonly errorHandler: ErrorHandlerService) { }

    getAll() {
        return this.http.get(`${this.baseUrl}/api/versions`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    getByPlatform(platformId: number) {
        return this.http.get(`${this.baseUrl}/api/platforms/${platformId}/versions`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    create(platformId: number, data: any) {
        return this.http.post(`${this.baseUrl}/api/platforms/${platformId}/versions`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    update(id: number, data: any) {
        return this.http.put(`${this.baseUrl}/api/versions/${id}`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    delete(id: number) {
        return this.http.delete(`${this.baseUrl}/api/versions/${id}`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }
}
