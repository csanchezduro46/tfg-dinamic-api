import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { GlobalErrorService } from '../../services/errors/global-error.service';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { faXmark } from '@fortawesome/pro-light-svg-icons';

@Component({
  selector: 'app-generic-error-popup',
  standalone: true,
  imports: [CommonModule, FontAwesomeModule],
  templateUrl: './generic-error-popup.component.html',
  styleUrl: './generic-error-popup.component.css'
})

export class GenericErrorPopupComponent {
  message: string | null = null;

  constructor(private readonly errorService: GlobalErrorService, library: FaIconLibrary) {
    this.errorService.error$.subscribe(msg => {this.message = msg});
    library.addIcons(faXmark);
  }

  close() {
    this.errorService.clear();
  }
}
