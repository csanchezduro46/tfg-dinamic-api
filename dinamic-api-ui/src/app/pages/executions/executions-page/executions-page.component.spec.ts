import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ExecutionsPageComponent } from './executions-page.component';

describe('ExecutionsPageComponent', () => {
  let component: ExecutionsPageComponent;
  let fixture: ComponentFixture<ExecutionsPageComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ExecutionsPageComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ExecutionsPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
