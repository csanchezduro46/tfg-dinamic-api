import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ScheduledExecutionModalComponent } from './scheduled-execution-modal.component';

describe('ScheduledExecutionModalComponent', () => {
  let component: ScheduledExecutionModalComponent;
  let fixture: ComponentFixture<ScheduledExecutionModalComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ScheduledExecutionModalComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ScheduledExecutionModalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
