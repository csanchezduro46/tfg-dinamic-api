import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { User } from '../../../core/models/user.model';
import { PlatformVersionService } from '../../../shared/services/api/platform-version.service';
import { PlatformService } from '../../../shared/services/api/platform.service';
import { AuthService } from '../../../shared/services/oauth/auth.service';
import { ConfirmPopupComponent } from '../../../shared/ui/confirm-popup/confirm-popup.component';
import { PlatformVersionFormComponent } from '../platform-version-form/platform-version-form.component';

@Component({
  selector: 'app-platform-versions-page',
  imports: [CommonModule, FontAwesomeModule, ConfirmPopupComponent, PlatformVersionFormComponent],
  standalone: true,
  templateUrl: './platform-versions-page.component.html',
  styleUrl: './platform-versions-page.component.css'
})
export class PlatformVersionsPageComponent implements OnInit {
  versions: any[] = [];
  loading = false;
  user!: User;
  admin: boolean = false;

  editing: any = null;
  confirmDeleteId: number | null = null;
  showFormPopup = false;
  platforms: any[] = [];;

  constructor(private readonly versionService: PlatformVersionService, library: FaIconLibrary,
    private readonly auth: AuthService, private readonly platformService: PlatformService) {
    library.addIconPacks(fas);
  }

  ngOnInit(): void {
    this.user = this.auth.getUser();
    this.admin = this.auth.hasRole('admin');
    this.platformService.getAll().subscribe({
      next: (data) => this.platforms = data
    });
    this.fetch();
  }

  fetch(): void {
    this.loading = true;
    this.versionService.getAll().subscribe({
      next: (data) => {
        this.versions = data;
        this.loading = false;
      },
      error: () => {
        this.loading = false;
        alert('Error al cargar las versiones');
      }
    });
  }

  onEdit(version: any): void {
    this.editing = version;
    this.showFormPopup = true;
  }
  
  onNew(): void {
    this.editing = null;
    this.showFormPopup = true;
  }

  onSaved(): void {
    this.showFormPopup = false;
    this.editing = null;
    setTimeout(() => {
      this.fetch();
    }, 2000);
  }

  askDelete(id: number): void {
    this.confirmDeleteId = id;
  }

  confirmDelete(): void {
    if (this.confirmDeleteId) {
      this.versionService.delete(this.confirmDeleteId).subscribe({
        next: () => {
          this.versions = this.versions.filter(v => v.id !== this.confirmDeleteId);
          this.confirmDeleteId = null;
          this.fetch();
        },
      });
    }
  }
}
