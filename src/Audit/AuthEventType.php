<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Audit;

/**
 * The most common audit event types (convenience: the type in AuditEvent is a
 * free-form string, so bridges can add their own, e.g. 'fortify.login.succeeded').
 */
enum AuthEventType: string
{
    case EmailOtpRequested = 'email_otp.requested';
    case EmailOtpSent = 'email_otp.sent';
    case EmailOtpVerified = 'email_otp.verified';
    case EmailOtpFailed = 'email_otp.failed';
    case StepUpRequired = 'step_up.required';
    case StepUpVerified = 'step_up.verified';
    case StepUpFailed = 'step_up.failed';
    case LoginSucceeded = 'login.succeeded';
    case LoginFailed = 'login.failed';
    case Logout = 'logout';
    case RecoveryRequested = 'recovery.requested';
    case RecoveryCompleted = 'recovery.completed';
    case RiskAnomalyDetected = 'risk.anomaly.detected';
}
