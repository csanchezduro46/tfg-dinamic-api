import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ScheduledExecutionsPageComponent } from './scheduled-executions-page.component';

describe('ScheduledExecutionsPageComponent', () => {
  let component: ScheduledExecutionsPageComponent;
  let fixture: ComponentFixture<ScheduledExecutionsPageComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ScheduledExecutionsPageComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ScheduledExecutionsPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
