import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { ApiCallService } from '../../../shared/services/api/api-call.service';

@Component({
  selector: 'app-platform-apis-page',
  imports: [CommonModule, FontAwesomeModule],
  standalone: true,
  templateUrl: './platform-apis-page.component.html',
  styleUrl: './platform-apis-page.component.css'
})
export class PlatformApisPageComponent implements OnInit {
  apis: any[] = [];
  loading = false;

  constructor(private readonly apisService: ApiCallService) { }

  ngOnInit(): void {
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

  onDelete(id: number): void {
    if (!confirm('¿Eliminar esta API?')) return;
    this.apisService.delete(id).subscribe({
      next: () => {
        this.apis = this.apis.filter(a => a.id !== id);
      },
      error: () => alert('Error al eliminar')
    });
  }
}
