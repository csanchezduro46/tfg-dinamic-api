import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { User } from '../../../core/models/user.model';
import { PlatformVersionService } from '../../../shared/services/api/platform-version.service';
import { AuthService } from '../../../shared/services/oauth/auth.service';

@Component({
  selector: 'app-platform-versions-page',
  imports: [CommonModule, FontAwesomeModule],
  standalone: true,
  templateUrl: './platform-versions-page.component.html',
  styleUrl: './platform-versions-page.component.css'
})
export class PlatformVersionsPageComponent implements OnInit {
  versions: any[] = [];
  loading = false;
  user!: User;

  constructor(private readonly versionService: PlatformVersionService, library: FaIconLibrary,
    private readonly auth: AuthService) {
    library.addIconPacks(fas);
  }

  ngOnInit(): void {
    this.user = this.auth.getUser();
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

  onDelete(id: number): void {
    if (!confirm('¿Eliminar esta versión?')) return;
    this.versionService.delete(id).subscribe({
      next: () => {
        this.versions = this.versions.filter(v => v.id !== id);
      },
      error: () => alert('Error al eliminar')
    });
  }
}
