import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { faPen, faTrash } from '@fortawesome/free-solid-svg-icons';
import { PlatformConnectionService } from '../../../shared/services/api/platform-connection.service';

@Component({
  selector: 'app-external-integrations-page',
  templateUrl: './external-integrations-page.component.html',
  styleUrl: './external-integrations-page.component.css',
  standalone: true,
  imports: [CommonModule, FontAwesomeModule]
})
export class ExternalIntegrationsPageComponent implements OnInit {
  platforms: any[] = [];
  loading = false;

  constructor(private readonly platformConnections: PlatformConnectionService, library: FaIconLibrary) { 
    library.addIcons(faPen, faTrash);
  }

  ngOnInit(): void {
    this.fetchPlatforms();
  }

  fetchPlatforms(): void {
    this.loading = true;
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

  onDelete(id: number): void {
    if (!confirm('¿Estás seguro de eliminar esta conexión externa?')) return;

    this.platformConnections.delete(id)
      .subscribe({
        next: () => {
          this.platforms = this.platforms.filter(p => p.id !== id);
        },
        error: () => alert('Error al eliminar')
      });
  }
}