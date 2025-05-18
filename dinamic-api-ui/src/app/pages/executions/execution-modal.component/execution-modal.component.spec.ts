import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ExecutionModalComponent } from './execution-modal.component';

describe('ExecutionModalComponent', () => {
  let component: ExecutionModalComponent;
  let fixture: ComponentFixture<ExecutionModalComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ExecutionModalComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ExecutionModalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
