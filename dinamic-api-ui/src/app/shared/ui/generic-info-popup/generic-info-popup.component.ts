import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fal } from '@fortawesome/pro-light-svg-icons';
import { GlobalInfoService } from '../../services/generic/global-info.service';
import { GlobalSuccessService } from '../../services/generic/global-success.service';

@Component({
  selector: 'app-generic-info-popup',
  imports: [CommonModule, FontAwesomeModule],
  templateUrl: './generic-info-popup.component.html',
  styleUrl: './generic-info-popup.component.css'
})
export class GenericInfoPopupComponent {
  message!: any | undefined;
  title: string | undefined = 'Operación realizada correctamente';
  icon: string = 'check';

  constructor(private readonly infoService: GlobalInfoService, library: FaIconLibrary) {
    this.infoService.success$.subscribe(data => {
      this.message = data?.message;
      this.title = data?.title;
      this.icon = data?.icon ?? 'check';
    });
    library.addIconPacks(fal);
  }

  close() {
    this.infoService.clear();
  }
}
