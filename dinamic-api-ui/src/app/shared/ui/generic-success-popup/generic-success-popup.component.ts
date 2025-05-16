import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { faCheck } from '@fortawesome/pro-light-svg-icons';
import { GlobalSuccessService } from '../../services/generic/global-success.service';

@Component({
  selector: 'app-generic-success-popup',
  standalone: true,
  imports: [CommonModule, FontAwesomeModule],
  templateUrl: './generic-success-popup.component.html',
  styleUrl: './generic-success-popup.component.css'
})
export class GenericSuccessPopupComponent {
  message!: string | undefined;
  title: string | undefined = 'Operación realizada correctamente';

  constructor(private readonly successService: GlobalSuccessService, library: FaIconLibrary) {
    this.successService.success$.subscribe(data => {
      this.message = data?.message;
      this.title = data?.title;
    });
    library.addIcons(faCheck);
  }

  close() {
    this.successService.clear();
  }
}
