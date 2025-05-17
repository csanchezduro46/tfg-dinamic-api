import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { faPen, faTrash } from '@fortawesome/free-solid-svg-icons';
import { DatabaseConnectionService } from '../../../shared/services/api/database-connection.service';

@Component({
  standalone: true,
  selector: 'app-database-schema-page',
  imports: [CommonModule, FontAwesomeModule],
  templateUrl: './database-schema-page.component.html',
  styleUrl: './database-schema-page.component.css'
})
export class DatabaseSchemaPageComponent implements OnInit {
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
