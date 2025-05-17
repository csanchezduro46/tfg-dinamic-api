import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { faPen, faTrash } from '@fortawesome/free-solid-svg-icons';
import { DatabaseConnectionService } from '../../../shared/services/api/database-connection.service';

@Component({
  standalone: true,
  selector: 'app-databases-list-page',
  imports: [CommonModule, FontAwesomeModule],
  templateUrl: './databases-list-page.component.html',
  styleUrl: './databases-list-page.component.css'
})
export class DatabasesListPageComponent implements OnInit {
  databases: any[] = [];
  loading = false;

  constructor(private readonly dbService: DatabaseConnectionService, library: FaIconLibrary) {
    library.addIcons(faPen, faTrash);
  }

  ngOnInit(): void {
    this.fetchDatabases();
  }

  fetchDatabases(): void {
    this.loading = true;
    this.dbService.getMine()
      .subscribe({
        next: (data) => {
          this.databases = data;
          this.loading = false;
        },
        error: () => {
          this.loading = false;
          alert('Error al cargar las conexiones de BBDD');
        }
      });
  }

  onDelete(id: number): void {
    if (!confirm('¿Estás seguro de eliminar esta conexión a BBDD?')) return;

    this.dbService.delete(id)
      .subscribe({
        next: () => {
          this.databases = this.databases.filter(d => d.id !== id);
        },
        error: () => alert('Error al eliminar la conexión')
      });
  }
}
