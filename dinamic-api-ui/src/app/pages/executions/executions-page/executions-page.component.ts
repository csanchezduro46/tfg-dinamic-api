import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { fal } from '@fortawesome/pro-light-svg-icons';
import { ExecutionService } from '../../../shared/services/api/execution.service';

@Component({
  selector: 'app-executions-page',
  imports: [CommonModule, FontAwesomeModule],
  templateUrl: './executions-page.component.html',
  styleUrl: './executions-page.component.css'
})
export class ExecutionsPageComponent implements OnInit {
  executions: any = [];
  loading = false;

  constructor(private readonly executionsService: ExecutionService, library: FaIconLibrary) {
    library.addIconPacks(fal, fas);
  }

  ngOnInit(): void {
    this.fetchExecutions();
  }

  fetchExecutions(): void {
    this.loading = true;
    this.executionsService.getAll()
      .subscribe({
        next: (data) => {
          this.executions = data;
          this.loading = false;
        },
        error: () => {
          this.loading = false;
          alert('Error al cargar las ejecuciones');
        }
      });
  }
}
