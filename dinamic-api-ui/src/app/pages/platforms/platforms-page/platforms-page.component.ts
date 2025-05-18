import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { User } from '../../../core/models/user.model';
import { PlatformService } from '../../../shared/services/api/platform.service';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';
import { AuthService } from '../../../shared/services/oauth/auth.service';
import { ConfirmPopupComponent } from '../../../shared/ui/confirm-popup/confirm-popup.component';
import { PlatformFormComponent } from '../platform-form/platform-form.component';

@Component({
  selector: 'app-platforms-page',
  imports: [CommonModule, FontAwesomeModule, ConfirmPopupComponent, PlatformFormComponent],
  standalone: true,
  templateUrl: './platforms-page.component.html',
  styleUrl: './platforms-page.component.css'
})
export class PlatformsPageComponent implements OnInit {
  platforms: any[] = [];
  loading = false;
  user!: User;
  admin: boolean = false;

  editing: any = null;
  confirmDeleteId: number | null = null;
  showFormPopup = false;

  constructor(private readonly platformService: PlatformService, library: FaIconLibrary,
    private readonly auth: AuthService, private readonly globalSuccessService: GlobalSuccessService) {
    library.addIconPacks(fas);
  }

  ngOnInit(): void {
    this.user = this.auth.getUser();
    this.admin = this.auth.hasRole('admin');
    this.fetch();
  }

  fetch(): void {
    this.loading = true;
    this.platformService.getAll().subscribe({
      next: (data) => {
        this.platforms = data;
        this.loading = false;
      },
      error: () => {
        this.loading = false;
        alert('Error al cargar plataformas');
      }
    });
  }

  onEdit(platform: any): void {
    this.editing = platform;
    this.showFormPopup = true;
  }

  onNew(): void {
    this.editing = null;
    this.showFormPopup = true;
  }

  onSaved(): void {
    this.showFormPopup = false;
    const msg = this.editing ? 'La plataforma ha sido editada correctamente.' : 'La plataforma ha sido creada correctamente.';
    this.globalSuccessService.show(msg, 'Operación realizada correctamente');
    this.editing = null;
    setTimeout(() => {
      this.fetch();
    }, 1000);
  }

  askDelete(id: number): void {
    this.confirmDeleteId = id;
  }

  confirmDelete(): void {
    if (this.confirmDeleteId) {
      this.platformService.delete(this.confirmDeleteId).subscribe({
        next: () => {
          this.platforms = this.platforms.filter(p => p.id !== this.confirmDeleteId);
          this.confirmDeleteId = null;
          this.fetch();
        }
      });
    }
  }
}
