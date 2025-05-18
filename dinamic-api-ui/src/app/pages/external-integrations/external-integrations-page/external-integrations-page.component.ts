import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { RouterModule } from '@angular/router';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { faPen, faTrash } from '@fortawesome/free-solid-svg-icons';
import { PlatformConnectionCredentialsService } from '../../../shared/services/api/platform-connection-credentials.service';
import { PlatformConnectionService } from '../../../shared/services/api/platform-connection.service';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';
import { AuthService } from '../../../shared/services/oauth/auth.service';
import { ConfirmPopupComponent } from '../../../shared/ui/confirm-popup/confirm-popup.component';

@Component({
  selector: 'app-external-integrations-page',
  templateUrl: './external-integrations-page.component.html',
  styleUrl: './external-integrations-page.component.css',
  standalone: true,
  imports: [CommonModule, FontAwesomeModule, ConfirmPopupComponent, RouterModule]
})
export class ExternalIntegrationsPageComponent implements OnInit {
  platforms: any[] = [];
  loading = false;
  admin: boolean = false;

  editing: any = null;
  confirmDeleteId: number | null = null;
  showFormPopup = false;
  loadingConnection: boolean = false;

  constructor(private readonly platformConnections: PlatformConnectionService, library: FaIconLibrary,
    private readonly auth: AuthService, private readonly globalSuccessService: GlobalSuccessService,
    private readonly connectionCredentialService: PlatformConnectionCredentialsService) {
    library.addIcons(faPen, faTrash);
  }

  ngOnInit(): void {
    this.admin = this.auth.hasRole('admin');
    this.fetchPlatforms();
  }

  fetchPlatforms(): void {
    this.loading = true;
    if (this.admin) {
      this.platformConnections.getAll()
        .subscribe({
          next: (data: any) => {
            this.platforms = data;
            this.loading = false;
          },
          error: () => {
            this.loading = false;
            alert('Error al cargar las conexiones de plataformas externas');
          }
        });
    } else {
      this.platformConnections.getMine()
        .subscribe({
          next: (data: any) => {
            this.platforms = data;
            this.loading = false;
          },
          error: () => {
            this.loading = false;
            alert('Error al cargar las conexiones de plataformas externas');
          }
        });
    }
  }

  askDelete(id: number): void {
    this.confirmDeleteId = id;
  }

  confirmDelete(): void {
    if (this.confirmDeleteId) {
      this.platformConnections.delete(this.confirmDeleteId).subscribe({
        next: () => {
          this.platforms = this.platforms.filter(p => p.id !== this.confirmDeleteId);
          this.confirmDeleteId = null;
          this.fetchPlatforms();
        }
      });
    }
  }

  testConnection(connection: any) {
    this.loadingConnection = true;
    this.connectionCredentialService.test(connection.id).subscribe({
      next: () => {
        this.globalSuccessService.show('La conexión externa es correcta.', 'Conexión correcta');
        this.loadingConnection = false;
      }, error: () => this.loadingConnection = false
    });
  }
}
