import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { User } from '../../../core/models/user.model';
import { ApiCallService } from '../../../shared/services/api/api-call.service';
import { ApiGroupService } from '../../../shared/services/api/api-group.service';
import { PlatformVersionService } from '../../../shared/services/api/platform-version.service';
import { PlatformService } from '../../../shared/services/api/platform.service';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';
import { AuthService } from '../../../shared/services/oauth/auth.service';
import { ConfirmPopupComponent } from '../../../shared/ui/confirm-popup/confirm-popup.component';
import { PlatformApiFormComponent } from '../platform-api-form/platform-api-form.component';

@Component({
  selector: 'app-platform-apis-page',
  imports: [CommonModule, FontAwesomeModule, ConfirmPopupComponent, PlatformApiFormComponent],
  standalone: true,
  templateUrl: './platform-apis-page.component.html',
  styleUrl: './platform-apis-page.component.css'
})
export class PlatformApisPageComponent implements OnInit {
  apis: any[] = [];
  loading = false;
  user!: User;
  admin: boolean = false;

  editing: any = null;
  confirmDeleteId: number | null = null;
  showFormPopup = false;

  platforms: any[] = [];
  versions: any[] = [];
  groups: any[] = [];

  constructor(private readonly apisService: ApiCallService, private readonly auth: AuthService,
    private readonly platformService: PlatformService, private readonly versionService: PlatformVersionService,
    private readonly groupService: ApiGroupService, private readonly globalSuccessService: GlobalSuccessService) { }

  ngOnInit(): void {
    this.user = this.auth.getUser();
    this.admin = this.auth.hasRole('admin');
    this.platformService.getAll().subscribe({
      next: (data) => this.platforms = data
    });
    this.versionService.getAll().subscribe({
      next: (data) => this.versions = data
    });
    this.groupService.getAll().subscribe({
      next: (data) => this.groups = data
    });
    this.fetch();
  }

  fetch(): void {
    this.loading = true;
    this.apisService.getAll().subscribe({
      next: (data) => {
        this.apis = data;
        this.loading = false;
      },
      error: () => {
        this.loading = false;
        alert('Error al cargar las APIs');
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
    const msg = this.editing ? 'La API ha sido editada correctamente.' : 'La API ha sido creada correctamente.';
    this.globalSuccessService.show(msg, 'Operación realizada correctamente');
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
      this.apisService.delete(this.confirmDeleteId).subscribe({
        next: () => {
          this.apis = this.apis.filter(api => api.id !== this.confirmDeleteId);
          this.confirmDeleteId = null;
          this.fetch();
        },
      });
    }
  }
}
