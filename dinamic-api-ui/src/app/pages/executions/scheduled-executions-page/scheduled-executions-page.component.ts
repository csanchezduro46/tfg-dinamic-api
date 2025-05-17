import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { ExecutionService } from '../../../shared/services/api/execution.service';

@Component({
  selector: 'app-scheduled-executions-page',
  imports: [CommonModule, FontAwesomeModule],
  standalone: true,
  templateUrl: './scheduled-executions-page.component.html',
  styleUrl: './scheduled-executions-page.component.css'
})
export class ScheduledExecutionsPageComponent implements OnInit {
  scheduled: any[] = [];
  loading = false;

  constructor(private readonly execService: ExecutionService, library: FaIconLibrary) {
    library.addIconPacks(fas)
  }

  ngOnInit(): void {
    this.loading = true;
    this.execService.getAll().subscribe({
      next: (data) => {
        this.scheduled = data;
        this.loading = false;
      },
      error: () => {
        this.loading = false;
        alert('Error al cargar ejecuciones programadas');
      }
    });
  }

  onDelete(id: number): void {
    if (!confirm('¿Eliminar esta ejecución programada?')) return;
    this.execService.delete(id).subscribe({
      next: () => {
        this.scheduled = this.scheduled.filter(e => e.id !== id);
      },
      error: () => alert('Error al eliminar')
    });
  }
}
