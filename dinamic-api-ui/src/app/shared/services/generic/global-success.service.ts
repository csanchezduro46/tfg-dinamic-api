import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class GlobalSuccessService {
  private readonly successSubject = new BehaviorSubject<{message: string, title: string} | null>(null);
  success$ = this.successSubject.asObservable();

  show(message: string, title: string = '') {
    this.successSubject.next({message: message, title: title});
  }

  clear() {
    this.successSubject.next(null);
  }
}
