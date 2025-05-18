import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PlatformVersionFormComponent } from './platform-version-form.component';

describe('PlatformVersionFormComponent', () => {
  let component: PlatformVersionFormComponent;
  let fixture: ComponentFixture<PlatformVersionFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PlatformVersionFormComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PlatformVersionFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
