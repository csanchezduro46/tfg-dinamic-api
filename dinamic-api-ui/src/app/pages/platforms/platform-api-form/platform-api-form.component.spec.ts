import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PlatformApiFormComponent } from './platform-api-form.component';

describe('PlatformApiFormComponent', () => {
  let component: PlatformApiFormComponent;
  let fixture: ComponentFixture<PlatformApiFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PlatformApiFormComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PlatformApiFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
