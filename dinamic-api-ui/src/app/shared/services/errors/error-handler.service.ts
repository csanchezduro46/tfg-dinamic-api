import { Injectable } from '@angular/core';
import { throwError } from 'rxjs';
import { GlobalErrorService } from './global-error.service';

@Injectable({ providedIn: 'root' })
export class ErrorHandlerService {
  constructor(private readonly globalError: GlobalErrorService) { }

  handle(error: any) {
    console.log(error)
    const message = error?.error?.msg || error?.error?.message || 'Ha ocurrido un error inesperado.';
    this.globalError.show(message);
    return throwError(() => error);
  }
}
