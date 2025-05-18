import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class GlobalInfoService {
  private readonly successSubject = new BehaviorSubject<{message: any, title: string, icon: string} | null>(null);
  success$ = this.successSubject.asObservable();

  show(message: any, title: string = '', icon: string = 'check') {
    this.successSubject.next({message: message, title: title, icon: icon});
  }

  clear() {
    this.successSubject.next(null);
  }
}
