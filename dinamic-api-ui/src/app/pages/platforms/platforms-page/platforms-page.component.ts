import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { User } from '../../../core/models/user.model';
import { PlatformService } from '../../../shared/services/api/platform.service';
import { AuthService } from '../../../shared/services/oauth/auth.service';

@Component({
  selector: 'app-platforms-page',
  imports: [CommonModule, FontAwesomeModule],
  standalone: true,
  templateUrl: './platforms-page.component.html',
  styleUrl: './platforms-page.component.css'
})
export class PlatformsPageComponent implements OnInit {
  platforms: any[] = [];
  loading = false;
  user!: User;

  constructor(private readonly platformService: PlatformService, library: FaIconLibrary,
    private readonly auth: AuthService) {
    library.addIconPacks(fas);
  }

  ngOnInit(): void {
    this.user = this.auth.getUser();
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

  onDelete(id: number): void {
    if (!confirm('¿Eliminar esta plataforma?')) return;
    this.platformService.delete(id).subscribe({
      next: () => {
        this.platforms = this.platforms.filter(p => p.id !== id);
      },
      error: () => alert('Error al eliminar')
    });
  }
}
