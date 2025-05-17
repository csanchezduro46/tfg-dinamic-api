import { Component, Input, Output, EventEmitter } from '@angular/core';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fal } from '@fortawesome/pro-light-svg-icons';

@Component({
  selector: 'app-confirm-popup',
  imports: [FontAwesomeModule],
  templateUrl: './confirm-popup.component.html',
  styleUrl: './confirm-popup.component.css'
})
export class ConfirmPopupComponent {
  @Input() title!: string;
  @Input() message!: string;
  @Input() confirmLabel?: string;
  @Input() cancelLabel?: string;

  @Output() confirm = new EventEmitter<void>();
  @Output() cancel = new EventEmitter<void>();

  constructor(library: FaIconLibrary) {
    library.addIconPacks(fal);
  }

  close() { }
}
